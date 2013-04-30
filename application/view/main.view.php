<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <title><?=(isset($title) ? $title : 'TheMovieDB API')?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="MobileOptimized" content="320">
    <meta name="HandheldFriendly" content="True">

    <link rel="stylesheet" href="/css/reset.css" type="text/css">
    <link rel="stylesheet" href="/css/icon-fonts.css" type="text/css">
    <link rel="stylesheet/less" type="text/css" href="/css/style.less">
    <script src="/scripts/less.min.js" type="text/javascript"></script>
  </head>
  
  <body>
    <div class="wrapper">
      <form data-bind="submit: runQuery">
        <label for="queryString">
          <span>Actor/Actress</span>
          <input type="text" id="queryString" data-bind="value: form.queryString" placeholder="Robert Deniro" />
          <div class="error" data-bind="css: { active: validation.state.queryString }"><span class="icon-cross"></span> Please enter an actor or actresses name.</div>
        </label>
        <button>Find Movies</button>
      </form>

      <div class="push"></div>
    </div>
    <footer>
      <div class="content">
        Partial API implentation of <a href="http://docs.themoviedb.apiary.io/">http://docs.themoviedb.apiary.io/</a>
      </div>
    </footer>

    <script src="scripts/require-jquery.js" data-main="scripts/main"></script>
  </body>
</html>