<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Vir - Character</title>

    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
    <script src="js/setup.js"></script>
    <script src="js/ajax-post.js"></script>

    <link href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
    <link href="css/main.css" media="all" rel="stylesheet" type="text/css" />
    <link href="css/character.css" media="all" rel="stylesheet" type="text/css" />

  </head>

  <body>

    <div id="left">
      <div id="primary_stats" class="box">
        <div class="box_header">
          <h2 class="box_title">Primary Stats</h2>
        </div>
        <div class="box_content">
          <ul>
            {foreach $character->PrimaryStats->container as $stat}
            <li class="primary_stat">
              <h3>{$stat->name}</h3>
              <input name="{$stat->name}_value" type="text" value="{$stat->value}"/>
            </li>
            {/foreach}
          </ul>
        </div>
        <div class="box_footer">
          <input type="submit" id="primary_stat_update" value="Update Primary Stats" />
        </div>
      </div>

      <div id="secondary_stats" class="box">
        <div class="box_header">
          <h2 class="box_title">Secondary Stats</h2>
        </div>
        <div class="box_content">
          <ul>
            {foreach $character->SecondaryStats->container as $stat}
            <li>
              <strong>{$stat->name}</strong> {$stat->value}
            </li>
            {/foreach}
          </ul>
        </div>
        <div class="box_footer">
        </div>
      </div>

    </div>

    <div id="right">
      {foreach $character->ability_groups->container as $ability_group}
      <div id="{$ability_group->flat_name}_box" class="ability_group box">
        <div class="box_header">
          <h2 class="box_title">{$ability_group->name}</h2>
        </div>
        <div class="box_content">
          <p>
            <font class="positive larger">{$ability_group->primary}</font>,
            <font class="positive">{$ability_group->secondary}</font>,
            <font class="negative">{$ability_group->negative}</font>
          </p>
          <p class="level">{$ability_group->base_level}</p>
        </div>
        <div class="box_footer"></div>
      </div>
      {/foreach}
    </div>

  </body>

</html>
