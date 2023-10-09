<header>
  <nav>
    <div class="header-name">
      <h1 class="spaced">The Community Cookbook </h1>
    </div>

    <div>
      <div class="nav-ul">
        <div class="nav-li">
          <a class="<?php echo $page1; ?> page" href="/">Explore</a>
        </div>
        <?php if (!is_user_logged_in()) { ?>
          <div class="nav-li">
            <a class="login this" href="/login">Log In</a>
          </div>
        <?php } else { ?>
          <div class="nav-li">
            <a class="<?php echo $page2; ?> page" href="/contribute">Contribute</a>
          </div>
          <div class="stack">
            <div class="stack-item">
              <p class="logout">Hi, <?php echo $current_user['f_name']; ?></p>
            </div>

            <div class="stack-item">
              <a class="logout-link" href="<?php echo logout_url(); ?>"> Sign Out? </a>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </nav>
</header>
