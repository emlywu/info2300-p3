<?php
include("includes/init.php");

// get all tag_names from tags, store in an array of string tag_names only
$all_tags = exec_sql_query($db, "SELECT tag_name FROM tags")->fetchAll();
$all_tag_names = array();
foreach ($all_tags as $tg) {
  array_push($all_tag_names, $tg[0]);
}

$current_tags = array();

// base query to get the id of a tag
$get_tag_id = "SELECT id FROM tags WHERE (tag_name = :tag_name)";
$existing_tag = False;

//form fb
$dish_fb = "hidden";
$src_fb = "hidden";
$ing_fb = "hidden";
$ins_fb = "hidden";
$tags_fb = "hidden";
$new_tag_fb = "hidden";

$deleted = "hidden";
$show_recipe = '';
$form_valid = false;

// get id of this recipe, build edit and view urls
$recipe_id = (int)trim($_GET['id']);
$url = "/explore/view?" . http_build_query(array('id' => $recipe_id));
$edit_url = "/explore/view?" . http_build_query(array('edit' => $recipe_id));

$edit_mode = False;
$can_edit = False;

if (isset($_GET['edit'])) {
  $edit_mode = True;
  $recipe_id = (int)trim($_GET['edit']);
}

if ($recipe_id) {
  $records = exec_sql_query(
    $db,
    "SELECT * FROM recipes LEFT OUTER JOIN recipe_tags ON recipes.id = recipe_tags.recipe_id LEFT OUTER JOIN tags ON recipe_tags.tag_id = tags.id WHERE recipe_tags.recipe_id = :id;",
    array(':id' => $recipe_id)
  )->fetchAll();

  if (count($records) > 0) {
    $recipe = $records[0];
  } else {
    $recipe = NULL;
  }
}


// check if current user posted this recipe, if so, they are allowed to edit
if ($recipe) {
  if ($current_user['id'] == $recipe['user_id']) {
    $can_edit = True;
  } else {
    $can_edit = False;
    $edit_mode = False;
  }

  // sticky variables
  $sticky_dish = $recipe['dish_name'];
  $sticky_src = $recipe['source'];
  $sticky_ing = $recipe['ingredients'];
  $sticky_ins = $recipe['instructions'];

  if (isset($_POST['update'])) {
    $dish = trim($_POST['dish']); //untrusted
    $ingredients = trim($_POST['ingredients']); //untrusted
    $instructions = trim($_POST['instructions']); //untrusted
    $src = trim($_POST['source']); // untrusted
    $new_tag = trim($_POST['new-tag']); //untrusted
    $tags = trim($_POST['tags']); //untrusted
    $del_tags = trim($_POST['del-tags']); //untrusted

    $form_valid = True;

    if (empty($dish)) {
      $form_valid = False;
      $dish_fb = '';
    }

    if (empty($src)) {
      $form_valid = False;
      $src_fb = '';
    }

    if (empty($ingredients)) {
      $form_valid = False;
      $ing_fb = '';
    }

    if (empty($instructions)) {
      $form_valid = False;
      $ins_fb = '';
    }

    if (!empty($new_tag)) {
      $transform_new_tag = ucwords(str_replace('-', ' ', $new_tag));

      // check if added tag exists
      if (in_array($transform_new_tag, $all_tag_names)) {
        $existing_tag = True;
        $new_tag_fb = '';
        $form_valid = False;
      }
    }

    if (!empty($dish) && !empty($ingredients) && !empty($instructions) && !empty($src) && $form_valid) {
      exec_sql_query(
        $db,
        "UPDATE recipes SET dish_name = :dish, ingredients = :ingredients, instructions = :instructions, source = :source WHERE (id = :id);",
        array(
          ':id' => $recipe_id,
          ':dish' => $dish,
          ':ingredients' => $ingredients,
          ':instructions' => $instructions,
          ':source' => $src
        )
      );

      //if existing tag added, and that tag's value is not "None" then add to recipe_tags
      if (!empty($tags) && $tags != "none") {
        //if (!$existing_tag) {

        //get tag id, transform value
        $transform_tag = ucwords(str_replace('-', ' ', $tags));
        $tag_id = exec_sql_query(
          $db,
          $get_tag_id,
          array(':tag_name' => $transform_tag)
        )->fetchAll();

        //establish tag-recipe relation: insert into recipe_tags table
        $insert_tag_rel = exec_sql_query(
          $db,
          "INSERT INTO recipe_tags (tag_id, recipe_id) VALUES (:tag_id, :recipe_id)",
          array(
            ':tag_id' => $tag_id[0]['id'],
            ':recipe_id' => $recipe_id
          )
        );
      }

      // if new tag added
      if (!empty($new_tag) && !$existing_tag) {
        $transform_new_tag = ucwords(str_replace('-', ' ', $new_tag));

        // check if added tag exists
        if (in_array($transform_new_tag, $all_tag_names)) {
          $existing_tag = True;
          $new_tag_fb = '';
          $form_valid = False;
        } else {
          $existing_tag = False;

          $tag_param = array(
            ':tag_name' => $transform_new_tag
          );

          // create new tag in tags table
          $insert_tag = "INSERT INTO tags (tag_name) VALUES (:tag_name)";
          $insert_tag_q = exec_sql_query($db, $insert_tag, $tag_param);

          $tag_id = exec_sql_query(
            $db,
            $get_tag_id,
            $tag_param
          )->fetchAll();

          // insert tag into recipe_tags table, associate with this recipe
          $insert_tag_rel = exec_sql_query(
            $db,
            "INSERT INTO recipe_tags (tag_id, recipe_id) VALUES (:new_tag_id, :new_recipe_id)",
            array(
              ':new_tag_id' => $tag_id[0]['id'],
              ':new_recipe_id' => $recipe_id
            )
          );
        }
      }

      // if tag to delete is not none
      if (!empty($del_tags) && $del_tags != "none") {
        $del_tags_transformed = ucwords(str_replace('-', ' ', $del_tags));

        // get id of tag to delete
        $del_tag_id = exec_sql_query($db, $get_tag_id, array(':tag_name' => $del_tags_transformed))->fetchAll();
        $del_tag_q = exec_sql_query(
          $db,
          "DELETE FROM recipe_tags WHERE recipe_id = :recipe_id AND tag_id = :tag_id",
          array(
            ':recipe_id' => $recipe_id,
            ':tag_id' => $del_tag_id[0]['id']
          )
        );
      }

      // updated records
      $records = exec_sql_query(
        $db,
        "SELECT * FROM recipes LEFT OUTER JOIN recipe_tags ON recipes.id = recipe_tags.recipe_id LEFT OUTER JOIN tags ON recipe_tags.tag_id = tags.id WHERE recipe_tags.recipe_id = :id;",
        array(':id' => $recipe_id)
      )->fetchAll();
      $recipe = $records[0];
    } else {
      $sticky_dish = $dish;
      $sticky_src = $src;
      $sticky_ing = $ingredients;
      $sticky_ins = $instructions;
      $edit_mode = True;
    }
  }

  $url = "/explore/view?" . http_build_query(array('id' => $recipe_id));
  $get_tag_q = "SELECT tags.tag_name, recipe_tags.recipe_id FROM tags INNER JOIN recipe_tags ON recipe_tags.tag_id = tags.id INNER JOIN recipes ON recipe_tags.recipe_id = recipes.id WHERE recipe_tags.recipe_id = :recipe_id_param";
  $get_tags = exec_sql_query($db, $get_tag_q, array(':recipe_id_param' => $recipe[0]))->fetchAll();
}

if (isset($_POST['delete'])) {
  $del_recipe = "DELETE FROM recipes WHERE id = :recipe_id";
  $del_tag = "DELETE FROM recipe_tags WHERE recipe_id = :recipe_id";
  $del_params = array(':recipe_id' => $recipe_id);

  // run delete queries
  $delete_r = exec_sql_query($db, $del_recipe, $del_params);
  $delete_t = exec_sql_query($db, $del_tag, $del_params);
  $deleted = '';
  $show_recipe = "hidden";

  $path_to_file = "public/uploads/recipes/" . $recipe_id . "." . $recipe['file_ext']; //img_file? or recipe_id.ext?
  unlink($path_to_file);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="/public/styles/styles.css" />
  <title>View Recipe</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
  <?php if ($recipe) { ?>
    <div class="<?php echo $show_recipe; ?>">
      <?php if (!$edit_mode) { ?>
        <div class="recipe-view">
          <img class="view-img" alt="<?php echo $recipe['dish_name']; ?>" src="/public/uploads/recipes/<?php echo ($recipe_id . "." . $recipe['file_ext']); ?>" />

          <h2><?php echo $recipe['dish_name']; ?></h2>
          <a class="view-source" href="<?php echo $recipe['source']; ?>">Source </a>

          <div class="view-tag-div">
            <?php foreach ($get_tags as $tag) { ?>
              <p class="view-tags"><?php echo $tag['tag_name']; ?> </p>
            <?php } ?>
          </div>

          <p><strong>Ingredients: </strong><?php echo $recipe['ingredients']; ?> </p>
          <p><strong>Instructions: </strong><?php echo $recipe['instructions']; ?> </p>

          <div class="button-options">
            <?php if ($can_edit) { ?>
              <a class="btn" href="<?php echo $edit_url; ?>">Edit</a>
            <?php } ?>
            <a class="btn" href="/">Back</a>
          </div>
        </div>

      <?php } else { ?>
        <div class="edit">
          <img class="view-img space" alt="<?php echo $recipe['dish_name']; ?>" src="/public/uploads/recipes/<?php echo ($recipe_id . "." . $recipe['file_ext']); ?>" />
          <form action="<?php echo $url; ?>" method="post" novalidate>

            <p class="<?php echo ($dish_fb); ?> fb"> Please enter the name of the dish.</p>
            <div class="form-item">
              <label for="dish">Dish Name: </label>
              <input class="dish-width" type="text" id="dish" name="dish" value="<?php echo htmlspecialchars($sticky_dish); ?>" required /> <br />
            </div>

            <p class="<?php echo ($src_fb); ?> fb"> Please enter the source of the recipe. </p>
            <div class="form-item">
              <label for="source">Recipe source: </label>
              <input class="wide" type="text" id="source" name="source" placeholder="&quot;Mine&quot; or &quot;My family's&quot; etc. if personal recipes" value="<?php echo htmlspecialchars($sticky_src); ?>" required /> <br />
            </div>

            <p class="<?php echo ($ing_fb); ?> fb"> Please list the ingredients used in the dish. </p>
            <div class="form-item">
              <label for="ingredients">List Ingredients: </label><br />
              <textarea id="ingredients" name="ingredients" rows="10" cols="55" required><?php echo htmlspecialchars($sticky_ing); ?></textarea> <br />
            </div>

            <p class="<?php echo ($ins_fb); ?> fb"> Please list the steps to take to create this dish. </p>
            <div class="form-item">
              <label for="instructions">Recipe Instructions: </label><br />
              <textarea id="instructions" name="instructions" rows="15" cols="55" required><?php echo htmlspecialchars($sticky_ins); ?></textarea> <br />
            </div>

            <div class="form-item">
              <h3>Tags:</h3>
              <div class="view-tag-div">
                <?php
                foreach ($get_tags as $tag) { ?>
                  <p class="view-tags"><?php echo $tag['tag_name']; ?> </p>
                <?php } ?>
              </div>

              <?php foreach ($get_tags as $tag) {
                array_push($current_tags, $tag[0]);
              }
              ?>

              <label for="add-tag">Add a tag: </label>
              <select class="contr-elt" id="add-tag" name="tags">
                <option id="none" value="none">-- Select --</option>
                <?php foreach ($all_tags as $tag) {
                  $tag_name = $tag['tag_name'];
                  // can only add tags that the recipe is not already tagged with
                  if (!(in_array($tag_name, $current_tags))) {
                    $tag_val = str_replace(' ', '-', strtolower($tag_name));
                ?>
                    <option id="<?php echo htmlspecialchars($tag_val); ?>" value="<?php echo htmlspecialchars($tag_val); ?>" <?php echo $sticky_tags[$tag_val]; ?>>
                      <?php echo htmlspecialchars(ucfirst($tag_name)); ?>
                    </option>
                <?php }
                } ?>
              </select> <br />
            </div>

            <p class="<?php echo $new_tag_fb; ?> fb">The tag you have created already exists.</p>
            <div class="form-item">
              <label for="new-tag" id="create-tags">Create New Tag: </label>
              <input type="text" id="new-tag" name="new-tag" placeholder="Optional" />
            </div>

            <div class="form-item">
              <label for="del-tags">Remove a tag: </label>
              <select class="contr-elt" id="del-tags" name="del-tags">
                <option id="no-del-tags" value="none">-- Select --</option>
                <?php foreach ($get_tags as $tag) { ?>
                  <option class="view-tags" id="<?php echo $tag['tag_name']; ?>"><?php echo $tag['tag_name']; ?> </option>
                <?php } ?>
              </select>
            </div>

            <input class="btn" type="submit" id="update" name="update" value="Update" />
            <input class="del-btn" type="submit" name="delete" value="Delete Entry" />
          </form>
        </div>

      <?php } ?>
    </div>

    <div class="deleted-wrapper <?php echo $deleted ?>">
      <div class="deleted-recipe">
        <h2> This recipe has been successfully deleted. </h2>
        <div class="delete-btns">
          <a class="btn w-space" href="/">Back</a>
          <a class="btn w-space" href="/contribute">Add another recipe</a>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php include("includes/footer.php"); ?>

</body>

</html>
