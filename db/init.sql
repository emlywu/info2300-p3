CREATE TABLE users (
  id	     INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  f_name	 TEXT NOT NULL,
  l_name   TEXT NOT NULL,
  username TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL
);

CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE,
  last_login   TEXT NOT NULL,

  FOREIGN KEY(user_id) REFERENCES users(id)
);

/* USERS SEED DATA */
INSERT INTO users (id, f_name, l_name, username, password) VALUES (1, 'Ezra', 'Cornell', 'big_red123', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (2, 'Martha', 'Pollack', 'mpollack5', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (3, 'Spider', 'Man', 'spider12', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (4, 'Bat', 'Man', 'bat89', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (5, 'Joe', 'Biden', 'jb34', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (6, 'Kamala', 'Harris', 'vp_usa', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (7, 'Michael', 'Scott', 'ms77', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (8, 'Gordon', 'Ramsey', 'cook123', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (9, 'Bobby', 'Flay', 'bflay4', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
INSERT INTO users (id, f_name, l_name, username, password) VALUES (10, 'Guy', 'Fieri', 'flavor23', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');


/* RECIPES TABLE */

CREATE TABLE recipes (
  id	         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  dish_name	   TEXT NOT NULL,
  ingredients  TEXT NOT NULL,
  instructions TEXT NOT NULL,
  img_file     TEXT NOT NULL,
  file_ext     TEXT NOT NULL,
  source       TEXT NOT NULL,
  user_id      INTEGER NOT NULL,
  FOREIGN KEY(user_id) REFERENCES users(id)
);

/* RECIPES SEED DATA */
/* Source: https://www.loveandlemons.com/greek-salad/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  1,
  "Greek Salad",
  "1 English cucumber, 1 green bell pepper, 2 cups halved cherry tomatoes, 5 ounces feta cheese, 1/3 cup thinly sliced red onion, 1/3 cup pitted Kalamata olives, 1/3 cup fresh mint leaves",
  "In a small bowl, whisk together olive oil, vinegar, garlic, oregano, mustard, salt, and several grinds of pepper for the dressing.
  On a large platter, arrange the cucumber, green pepper, cherry tomatoes, feta cheese, red onions, and olives. Drizzle with the dressing and very gently toss. Sprinkle with a few generous pinches of oregano and top with the mint leaves. Season to taste and serve.",
  '1.jpeg',
  'jpeg',
  "https://www.loveandlemons.com/greek-salad/",
  2
);

/* Source: https://www.loveandlemons.com/heirloom-tomato-avocado-chickpea-salad/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  2,
  "Heirloom Tomato and Avocado Salad",
  "3 medium heirloom tomatoes, olive oil, sherry vinegar, 1/2 cup uncooked whole wheat orzo, 1 clove of garlic, arugula, 1/2 cup chickpeas (cooked & drained), 1/2 avocado, juice of 1/2 a small lemon, handful of fresh basil, pine nuts, salt & pepper",
  "Chop the tomatoes into approx 1-inch pieces. Place in a bowl with a few tablespoons of sherry vinegar, a splash of olive oil, 1/2 a smashed garlic clove (remove it later), and a few pinches of salt & pepper. Let the tomatoes marinate at room temp (stirring occasionally, while you prep everything else. Taste and adjust seasonings as it sits. Then cook the orzo in salted boiling water for 7-9 minutes or until al dente. While you're waiting for it, take a large bowl and rub the inside of it with the cut side of the other half of your garlic clove.Drain your orzo and place it (warm) into the bowl. Add a good glug of olive oil, then the arugula, chickpeas, lemon juice, salt & pepper, and toss. Taste and adjust seasonings. Dice your avocado and season it with a squeeze of lemon and a bit of salt. Place the orzo salad onto a serving plate (or just keep it in the same bowl). Take the tomatoes and drain out most of the liquid at the bottom of their bowl (this will keep your salad from becoming too watery). Find that smashed garlic clove and remove it. Place the tomatoes onto the salad with the diced avocado, basil and pine nuts. Taste and adjust seasonings one last time.",
  "2.jpeg",
  "jpeg",
  "https://www.loveandlemons.com/heirloom-tomato-avocado-chickpea-salad/",
  3
);

/* Source: https://www.delish.com/cooking/recipe-ideas/a19610233/how-to-make-best-grilled-cheese/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  3,
  'Grilled Cheese',
  "5 tbsp. softened butter, 4 slices sourdough bread, 2 cups shredded cheddar",
  "Spread 1 tablespoon butter on one side of each slice of bread. With butter side down, top each slice of bread with about 1/2 cup cheddar.
  In a skillet over medium heat, melt 1 tablespoon butter. Add two slices of bread, butter side down. Cook until bread is golden and cheese is starting to melt, about 2 minutes. Flip one piece of bread on top of the other and continue to cook until cheese is melty, about 30 seconds more.
  Repeat for the second sandwich, wiping skillet clean if necessary. ",
  "3.jpeg",
  "jpeg",
  "https://www.delish.com/cooking/recipe-ideas/a19610233/how-to-make-best-grilled-cheese/",
  8
);

/* Source: https://fitfoodiefinds.com/strawberry-banana-smoothie-recipe/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  4,
  'Strawberry Banana Smoothie',
  "frozen strawberries, frozen banana, Greek yogurt, vanilla extract, almond milk",
  "Place all ingredients into your high-speed blender. Blend on high until smooth. Option to add more milk as needed.",
  "4.jpeg",
  "jpeg",
  "https://fitfoodiefinds.com/strawberry-banana-smoothie-recipe/",
  9
);

/* Source: https://fitfoodiefinds.com/peanut-butter-banana-smoothie/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  5,
  'Peanut Butter Banana Smoothie',
  "2 cups frozen bananas, 1/2 cup Greek yogurt, 1/2 table spoon flax seeds, 1 cup almond milk, 1 teaspoon vanilla extract, 2 tablespoons peanut butter:",
  "Place all ingredients into a high-speed blender. Blend on high until smooth. Add more almond milk as needed. Serve immediately.",
  "5.jpeg",
  "jpeg",
  "https://fitfoodiefinds.com/peanut-butter-banana-smoothie/",
  1
);

/* Source: https://www.tasteofhome.com/recipes/turkey-focaccia-club/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  6,
  'Turkey Focaccia Club',
  "1/2 cup mayonnaise, 1/2 cup whole-berry cranberry sauce, 2 tablespoons toasted chopped pecans, 2 tablespoons Dijon mustard, 1 tablespoon honey, 1 loaf (8 ounces) focaccia bread, 3 lettuce leaves, 1/2 pound thinly sliced cooked turkey, 1/4 pound sliced Gouda cheese, 8 slices tomato, 6 cooked bacon strips",
  "In a small bowl, mix the first 5 ingredients until blended. Using a long serrated knife, cut focaccia horizontally in half. Spread cut sides with mayonnaise mixture. Layer bottom half with lettuce, turkey, cheese, tomato and bacon; replace bread top. Cut into wedges.",
  "6.jpeg",
  "jpeg",
  "https://www.tasteofhome.com/recipes/turkey-focaccia-club/",
  10
);

/* Source: https://www.tasteofhome.com/recipes/quick-cream-of-mushroom-soup/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  7,
  'Cream of Mushroom Soup',
  "2 tablespoons butter, 1/2 pound sliced fresh mushrooms, 1/4 cup chopped onion, 6 tablespoons all-purpose flour, 1/2 teaspoon salt, 1/8 teaspoon pepper, 2 cans (14-1/2 ounces each) chicken broth, 1 cup half-and-half cream",
  "In a large saucepan, heat butter over medium-high heat; saute mushrooms and onion until tender.
  Mix flour, salt, pepper and 1 can broth until smooth; stir into mushroom mixture. Stir in remaining can of broth. Bring to a boil; cook and stir until thickened, about 2 minutes. Reduce heat; stir in cream. Simmer, uncovered, until flavors are blended, about 15 minutes, stirring occasionally.",
  "7.png",
  "png",
  "https://www.tasteofhome.com/recipes/quick-cream-of-mushroom-soup/",
  4
);

/* Source: https://www.tasteofhome.com/recipes/broccoli-cheddar-soup/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  8,
  "Broccoli Cheddar Soup",
  "1/4 cup chopped onion, 1/4 cup cubed butter, 1/4 cup all-purpose flour, 1/4 teaspoon salt, 1/4 teaspoon pepper, 1-1/2 cups 2% milk, 3/4 cup chicken broth, 1 cup cooked chopped fresh or frozen broccoli, 1/2 cup shredded cheddar cheese",
  "In a small saucepan, saute onion in butter until tender. Stir in the flour, salt and pepper until blended; gradually add milk and broth. Bring to a boil; cook and stir until thickened, about 2 minutes.
  Add broccoli. Cook and stir until heated through. Remove from the heat; stir in cheese until melted.",
  "8.png",
  "png",
  "https://www.tasteofhome.com/recipes/broccoli-cheddar-soup/",
  5
);

/* Source: https://www.food.com/recipe/chocolate-chip-pancakes-45935 */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  9,
  'Chocolate Chip Pancakes',
  "1 1⁄4 cups flour, 1 tablespoon sugar, 1⁄4 teaspoon cinnamon, 1 tablespoon baking powder, 1/4 teaspoon salt, 2 eggs, 1 cup milk, 4 tablespoons melted butter, 3⁄4 teaspoon vanilla, 1⁄3 cup chocolate chips",
  "Preheat fry pan. Combine flour, sugar, cinnamon, baking powder and salt in a large bowl. Mix together wet ingredients and beat into dry mixture until smooth. Fold in chocolate chips. Pour or spoon batter into fry pan in desired quantity. Flip when top begins to bubble, then cook a minute more.",
  "9.jpeg",
  "jpeg",
  "https://www.food.com/recipe/chocolate-chip-pancakes-45935",
  8
);

/* Source: https://theclevermeal.com/10-minute-lemon-ricotta-pasta-with-spinach/ */
INSERT INTO recipes (id, dish_name, ingredients, instructions, img_file, file_ext, source, user_id) VALUES (
  10,
  'Lemon Ricotta Pasta and Spinach',
  "Pasta, Ricotta, Parmesan cheese, Baby spinach, Lemon, Extra virgin olive oil, Garlic, Salt and pepper",
  "Combine ricotta, parmesan, extra virgin olive oil, garlic, lemon zest, salt and pepper. Mix well, taste and make sure you’re happy with the seasoning. Meanwhile, cook your pasta until al dente. Reserve some pasta cooking water. Add spinach to the pot and cook for 1 minute. Drain pasta and spinach and return to the pot. Add the ricotta sauce, the cooking water, and stir well. Serve with a drizzle of olive oil, freshly grated Parmesan cheese, and lemon wedges (optional). Enjoy!",
  "10.jpeg",
  "jpeg",
  "https://theclevermeal.com/10-minute-lemon-ricotta-pasta-with-spinach/",
  10
);

/* TAGS TABLE */
CREATE TABLE tags (
  id        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  tag_name  TEXT NOT NULL
);

/* TAGS SEED DATA */
INSERT INTO tags (id, tag_name) VALUES (1, "Breakfast");
INSERT INTO tags (id, tag_name) VALUES (2, "Lunch");
INSERT INTO tags (id, tag_name) VALUES (3, "Dinner");
INSERT INTO tags (id, tag_name) VALUES (4, "Drinks");
INSERT INTO tags (id, tag_name) VALUES (5, "Dessert");
INSERT INTO tags (id, tag_name) VALUES (6, "Soups Salads");

/* RECIPE_TAGS TABLE */
CREATE TABLE recipe_tags (
  id        INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  tag_id    INTEGER NOT NULL,
  recipe_id INTEGER NOT NULL
);

/* RECIPE_TAGS SEED DATA */
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (1, 1, 9);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (2, 4, 4);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (3, 4, 5);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (4, 3, 10);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (5, 6, 8);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (6, 6, 7);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (7, 6, 1);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (8, 6, 2);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (9, 2, 3);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (10, 2, 6);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (11, 2, 8);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (12, 2, 7);
INSERT INTO recipe_tags (id, tag_id, recipe_id) VALUES (13, 2, 10);
