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
      <form data-bind="submit: searchActors">
        <label for="queryString">
          <span>Actor/Actress</span>
          <input type="text" id="queryString" class="focus" data-bind="value: form.queryString, valueUpdate: 'afterkeydown'" placeholder="Robert De Niro" />
          <div class="error" data-bind="css: { active: validation.state.queryString }"><span class="icon-cross"></span> Please enter an actor or actresses name.</div>
        </label>
        <button>Find Movies</button>
      </form>

      <div id="no-results" data-bind="css: { visible: !people().length && !searchingActors() }">
        <div class="message">Please use the form on the left to search for an actor or actress by name.</div>
      </div>
      <div id="results" data-bind="css: { visible: people().length }">
        <div id="actor-search-results">
          <div class="people-label">
            <span>Actors Found: </span>
            <span class="num-actors" data-bind="text: numPeopleResults"></span>
          </div>
          <div class="people" data-bind="foreach: people">
            <div class="person" data-bind="click: activate, css: { active: active }">
              <span class="name" data-bind="text: name"></span>
            </div>
          </div>
        </div>

        <div id="movies" data-bind="foreach: movies">
          <div class="movie">
            <div class="released">
              <span>Released on: </span>
              <span class="date" data-bind="text: releaseDate"></span>
            </div>
            <div class="title"><span data-bind="text: title"></span></div>
          </div>
        </div>
      </div>

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