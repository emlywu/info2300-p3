<?php
$page1 = "current";
include("includes/init.php");

// base query to return all recipe entries
$query = "SELECT *, COUNT(recipe_tags.recipe_id) as num_tags FROM recipes INNER JOIN recipe_tags ON recipes.id=recipe_tags.recipe_id INNER JOIN tags ON recipe_tags.tag_id=tags.id";
$group = " GROUP BY recipes.id";

// query to return all tags for a given recipe
$tag_query = "SELECT tags.tag_name, recipe_tags.recipe_id FROM tags INNER JOIN recipe_tags ON recipe_tags.tag_id = tags.id INNER JOIN recipes ON recipe_tags.recipe_id = recipes.id WHERE recipe_tags.recipe_id = :recipe_id_param";

/* -------------------------- FILTER + SEARCH --------------------------------- */
$search = NULL;
$sticky_search = '';
$has_search = False;
$has_where = False;

// get all tag_names from tags
$all_tags = exec_sql_query($db, "SELECT tag_name FROM tags")->fetchAll();

// filter variables
$sticky_tags = array();
$filter_exprs = '';
$filtered = False;

if (isset($_GET['search'])) {
  $search = trim($_GET['search-by']); //untrusted
  $params = array(':search' => $search);

  if (empty($search)) {
    $search = NULL;
  }

  // if something was searched, append it to the base search query
  if (!empty($search)) {
    $query = $query . " WHERE ((dish_name LIKE '%' || :search || '%')
    OR (ingredients LIKE '%' || :search || '%'))";
    $has_where = True;
    $has_search = True;
  }

  $sticky_search = $search; //tainted
}

if (isset($_GET['filter'])) {
  $target_tag = $_GET['tags'];
  // clean up tags and determine if a tag was selected
  foreach ($all_tags as $tag) {
    $tag_cleaned = str_replace(' ', '-', strtolower($tag[0]));

    if ($tag_cleaned == $target_tag) {
      $tag_to_filter = True;
    } else {
      $tag_to_filter = False;
    }

    $sticky_tags[$tag_cleaned] = ($tag_to_filter ? 'selected' : '');

    // if tag selected, construct filter expression for it
    if ($tag_to_filter && $tag_cleaned != "none") {
      $query = $query . ($has_where ? ' ' : ' WHERE ');
      $has_where = True;

      if ($has_search) {
        $query = $query . ($has_and ? ' ' : 'AND ');
        $has_and = True;
      }

      $filter_exprs = $filter_exprs . "(tags.tag_name = :tag_name)";
      $tag_to_search = ucwords(str_replace('-', ' ', $tag_cleaned));
      $params = array(':tag_name' => $tag_to_search);
    }
    $filtered = True;
  }
}

// if filter expression assembled, add it to the end of the base query
if ($filtered) {
  $query = $query . $filter_exprs;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="/public/styles/styles.css" />
  <title>Explore</title>
</head>

<body class="home">
  <?php include("includes/header.php"); ?>
  <div class="explore">

    <div class="filter-search-wrapper">

      <form action="/" method="get" novalidate>
        <div class="search-form">
          <label class="lbl" for="search-by">Search</label>
          <input class="search-input" type="text" id="search-by" name="search-by" placeholder="Search for recipes or by ingredients!" value="<?php echo htmlspecialchars($sticky_search); ?>" />
        </div>
        <div class="f-s-btns">
          <input class='btn-sm' type='submit' name="search" value="search">
          <a class="btn-sm" href="/">clear search</a>
        </div>


        <div class="filter-form">
          <label class="lbl" for="tags">Filter by tags</label>
          <select class="contr-elt" id="tags" name="tags">
            <option id="no-tags" value="none">-- Select --</option>
            <?php
            foreach ($all_tags as $tag) {
              $tag_val = str_replace(' ', '-', strtolower($tag['tag_name']));
            ?> <option id="<?php echo htmlspecialchars($tag_val); ?>" value="<?php echo htmlspecialchars($tag_val); ?>" <?php echo $sticky_tags[$tag_val]; ?>>
                <?php echo htmlspecialchars(ucfirst($tag['tag_name'])); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="f-s-btns">
          <input class='btn-sm' type='submit' name="filter" value="apply">
          <a class="btn-sm" href="/">clear filter</a>
        </div>
      </form>
    </div>

    <div class="catalog">

      <?php
      $records = exec_sql_query(
        $db,
        $query . $group,
        $params
      )->fetchAll();

      if (count($records) > 0) { ?>

        <?php
        foreach ($records as $record) { ?>
          <div class="tile">
            <img class="tile-img" src="/public/uploads/recipes/<?php echo $record['recipe_id'] . '.' . $record['file_ext'] ?>" alt="<?php echo $record['dish_name']; ?>" />
            <h3 class="dish-name"><?php echo $record['dish_name']; ?></h3>
            <?php
            $get_tag_params = array(':recipe_id_param' => $record[0]);
            $get_tags = exec_sql_query($db, $tag_query, $get_tag_params)->fetchAll();
            ?>
            <div class="tag-div">
              <?php foreach ($get_tags as $print_tag) { ?>
                <p class="tags"><?php echo htmlspecialchars($print_tag['tag_name']) ?> </p>
              <?php } ?>
            </div>
            <a class='btn get-recipe' href="/explore/view?<?php echo http_build_query(array('id' => $record['recipe_id'])); ?>">
              Get the recipe!
            </a>
          </div>
        <?php
        } ?>

      <?php } else { ?>
        <h2> No recipes found. </h2>
      <?php } ?>
    </div>
  </div>
  <?php include("includes/footer.php"); ?>
</body>

</html>
