<form data-bind="submit: searchActors">
  <label for="queryString">
    <span>Actor/Actress</span>
    <input type="text" id="queryString" class="focus" data-bind="value: form.queryString, valueUpdate: 'afterkeydown'" placeholder="Robert De Niro" />
    <div class="error" data-bind="css: { active: validation.state.queryString }"><span class="icon icon-warning"></span> Please enter an actor or actresses name.</div>
  </label>
  <button><span class="icon icon-search"></span> Find Movies</button>
</form>

<div id="no-results" data-bind="css: { visible: !people().length && !searchingActors() }">
  <div class="message">Please use the form on the left to search for an actor or actress by name.</div>
</div>
<div id="loading-results" data-bind="css: { visible: searchingActors }">
  <img src="/images/ajax-white.gif" />
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

  <div id="actor-floater" data-bind="foreach: person">
    <div class="actor">
      <div class="name" data-bind="text: name"></div>
    </div>
  </div>

  <div id="movies">
    <div id="movies-loading" data-bind="css: { visible: searchingMovies }">
      <img src="/images/ajax-blue.gif" />
    </div>
    <div data-bind="foreach: movies">
      <div class="movie">
        <div class="poster">
          <img data-bind="attr: { src: posterImageURL }" />
        </div>
        <table class="info">
          <tr class="released">
            <td class="label">Released:</td>
            <td class="content" data-bind="text: releaseDate"></td>
          </tr>
          <tr class="title">
            <td class="label">Title:</td>
            <td class="content" data-bind="text: title"></td>
          </tr>
          <tr class="character">
            <td class="label">Character:</td>
            <td class="content" data-bind="text: character"></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>