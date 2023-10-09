<?php
$page2 = "current";
include("includes/init.php");

// define max file size for images
define("MAX_FILE_SIZE", 1000000);

// get array of tags
$params = array();
$all_tags = exec_sql_query($db, "SELECT tag_name from tags", $params)->fetchAll();
$all_tag_names = array();
foreach ($all_tags as $tg) {
  array_push($all_tag_names, $tg[0]);
}

// base query to get tag id
$get_tag_id = "SELECT id FROM tags WHERE (tag_name = :tag_name)";

// initialize input fields to NULL
$dish = NULL;
$src = NULL;
$ingredients = NULL;
$instructions = NULL;
$tags = NULL;
$existing_tag = NULL;

$upload_filename = NULL;
$upload_ext = NULL;


// initialize sticky values
$sticky_dish = '';
$sticky_src = '';
$sticky_ing = '';
$sticky_ins = '';
$sticky_new_tag = '';
$sticky_tags = array();

// initialize all feedback to hidden
$dish_fb = "hidden";
$img_fb = "hidden";
$src_fb = "hidden";
$ing_fb = "hidden";
$ins_fb = "hidden";
$tags_fb = "hidden";
$new_tag_fb = "hidden";

// show form initially, hide confirmation
$show_form = '';
$show_confirmation = 'hidden';

// initialize form to be invalid
$form_valid = False;

// insert item validation
$insert_success = False;
$insert_fail = False;

if (isset($_POST['contribute'])) {
  $dish = trim($_POST['dish']); // untrusted
  $src = trim($_POST['source']); // untrusted
  $ingredients = trim($_POST['ingredients']); // untrusted
  $instructions = trim($_POST['instructions']); // untrusted
  $new_tag = trim($_POST['new-tag']); // untrusted
  $tags = $_POST['tags']; // untrusted
  $upload = $_FILES["img-file"]; // get image file

  $form_valid = True;

  // set sticky tags
  foreach ($all_tags as $tag) {
    $tag_cleaned = str_replace(' ', '-', strtolower($tag[0]));
    if ($tag_cleaned == $tags) {
      $tag_selected = True;
    } else {
      $tag_selected = False;
    }
    $sticky_tags[$tag_cleaned] = ($tag_selected ? 'selected' : '');
  }

  if (empty($dish)) {
    $form_valid = False;
    $dish_fb = '';
  }

  // check if upload successful
  if ($upload['error'] == UPLOAD_ERR_OK) {
    $upload_filename = basename($upload['name']);
    $upload_ext = strtolower(pathinfo($upload_filename, PATHINFO_EXTENSION));
  } else {
    $img_fb = '';
    $form_valid = False;
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

  if (empty($tags) || $tags == "none") {
    $form_valid = False;
    $tags_fb = '';
  }

  // check if new tag field is empty (OPTIONAL input field)
  if (!empty($new_tag)) {
    $transform_new_tag = ucwords(str_replace('-', ' ', $new_tag));

    // check if new tag already exists — if it does, display feedback, else tag does not exist
    if (in_array($transform_new_tag, $all_tag_names)) {
      $existing_tag = True;
      $new_tag_fb = '';
      $form_valid = False;
    } else {
      $existing_tag = False;
    }
  }

  if ($form_valid) {
    $show_form = "hidden";
    $show_confirmation = '';

    $db->beginTransaction();

    $result = exec_sql_query(
      $db,
      "INSERT INTO recipes (dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (:dish, :ingredients, :instructions, :img, :file_ext, :source, :user_id);",
      array(
        ':dish' => $dish,
        ':ingredients' => $ingredients,
        ':instructions' => $instructions,
        ':img' => $upload_filename,
        ':file_ext' => $upload_ext,
        ':source' => $src,
        ':user_id' => $current_user['id']
      )
    );

    if ($result) {
      $insert_success = True;
      $pkey_id = $db->lastInsertId("id");
      $new_path = "public/uploads/recipes/" . $pkey_id . "." . $upload_ext;
      move_uploaded_file($upload["tmp_name"], $new_path);

      // add new existing tags
      if (!empty($tags) && $tags != "none") {
        //get tag id
        $transform_tag = ucwords(str_replace('-', ' ', $tags));
        $tag_id = exec_sql_query(
          $db,
          $get_tag_id,
          array(':tag_name' => $transform_tag)
        )->fetchAll();

        //establish tag-recipe relation
        $insert_tag_rel = exec_sql_query(
          $db,
          "INSERT INTO recipe_tags (tag_id, recipe_id) VALUES (:tag_id, :recipe_id)",
          array(
            ':tag_id' => $tag_id[0]['id'],
            ':recipe_id' => $pkey_id
          )
        );
      }

      if (!empty($new_tag) && !($existing_tag)) {
        // create new tag
        $tag_param = array(
          ':tag_name' => $new_tag
        );
        $insert_tag = "INSERT INTO tags (tag_name) VALUES (:tag_name)";
        $insert_tag_q = exec_sql_query($db, $insert_tag, $tag_param);

        // establish tag relations
        $tag_id = exec_sql_query(
          $db,
          $get_tag_id,
          $tag_param
        )->fetchAll();

        $insert_tag_rel = exec_sql_query(
          $db,
          "INSERT INTO recipe_tags (tag_id, recipe_id) VALUES (:new_tag_id, :new_recipe_id)",
          array(
            ':new_tag_id' => $tag_id[0]['id'],
            ':new_recipe_id' => $pkey_id
          )
        );
      }
      $show_confirmation = '';
      $show_form = "hidden";
    } else {
      $insert_fail = True;
    }
    $db->commit();
  } else {
    $sticky_dish = $dish;
    $sticky_src = $src;
    $sticky_ing = $ingredients;
    $sticky_ins = $instructions;
    $sticky_new_tag = $new_tag;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="public/styles/styles.css" />
  <title>Contribute</title>
</head>

<body>
  <?php include("includes/header.php"); ?>
  <div class="<?php echo $show_form; ?>">
    <div class="contribute">
      <form class="contribute-form" action="/contribute" method="post" enctype="multipart/form-data" novalidate>
        <h2> Add a recipe to the Community Cookbook </h2>

        <p class="<?php echo ($dish_fb); ?> fb"> Please enter the name of the dish. </p>
        <div class="form-item">
          <label for="dish">Dish Name: </label>
          <input class="dish-width" type="text" id="dish" name="dish" value="<?php echo htmlspecialchars($sticky_dish); ?>" /> <br />
        </div>

        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />
        <p class="<?php echo $img_fb; ?> fb"> Please upload an image of the dish. The image you upload may be no larger than 10 MB</p>
        <div class="form-item">
          <label for="img">Upload Image of the Dish: </label>
          <input type="file" id="img" name="img-file" required /> <br />
        </div>

        <p class="<?php echo ($src_fb); ?> fb"> Please enter the source of the recipe. </p>
        <div class="form-item">
          <label for="source">Recipe source: </label>
          <input class="wide" type="text" id="source" name="source" placeholder="&quot;Mine&quot; or &quot;My family's&quot; etc. if personal recipes" value="<?php echo htmlspecialchars($sticky_src); ?>" /> <br />
        </div>

        <p class="<?php echo ($ing_fb); ?> fb"> Please list the ingredients used in the dish. </p>
        <div class="form-item">
          <label for="ingredients">List Ingredients: </label><br />
          <textarea id="ingredients" name="ingredients" rows="10" cols="55"><?php echo htmlspecialchars($sticky_ing); ?></textarea> <br />
        </div>

        <p class="<?php echo ($ins_fb); ?> fb"> Please list the steps to take to create this dish. </p>
        <div class="form-item">
          <label for="instructions">Recipe Instructions: </label><br />
          <textarea id="instructions" name="instructions" rows="15" cols="55"><?php echo htmlspecialchars($sticky_ins); ?></textarea> <br />
        </div>

        <p class="<?php echo $tags_fb; ?> fb">Please select one tag</p>
        <div class="form-item">
          <label for="add-tags-new" id="add-tags">Select one tag to get started! </label><br />
          <select class="contr-tag" id="add-tags-new" name="tags">
            <option value="none">None</option>
            <?php
            foreach ($all_tags as $tag) {
              $tag_val = str_replace(' ', '-', strtolower($tag['tag_name']));
            ?>
              <option id="<?php echo htmlspecialchars($tag_val); ?>" value="<?php echo htmlspecialchars($tag_val); ?>" <?php echo $sticky_tags[$tag_val]; ?>>
                <?php
                echo htmlspecialchars(ucfirst($tag['tag_name'])); ?>
              </option>
            <?php
            } ?>
          </select>

          <br />
          <p class="<?php echo ($new_tag_fb); ?> fb"> The tag you have entered already exists. Please select it from the list of tags above, or create a new one. </p>
          <label for="new-tag-contr" id="new-tag">Create New Tag: </label>
          <input type="text" id="new-tag-contr" name="new-tag" placeholder="Optional" value="<?php echo htmlspecialchars($sticky_new_tag); ?>" />
        </div>

        <input class="btn contrb" type="submit" name="contribute" value="Add!" />
      </form>
    </div>
  </div>

  <div class="confirmation-wrapper <?php echo $show_confirmation; ?>">
    <div class="confirmation">
      <?php $sticky_dish = $dish; ?>
      <h1>You just added "<?php echo htmlspecialchars($sticky_dish); ?>" to the Community Cookbook! </h1>
      <a class="btn" href="/explore/view?id=<?php echo htmlspecialchars($pkey_id); ?>">View it Here</a>
    </div>
  </div>

  <?php include("includes/footer.php"); ?>
</body>

</html>
