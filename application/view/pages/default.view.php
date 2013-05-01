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

  <div id="movies">
    <div id="movies-loading" data-bind="css: { visible: searchingMovies }">
      <img src="/images/ajax-blue.gif" />
    </div>
    <div data-bind="foreach: movies">
      <div class="movie">
        <div class="released">
          <span>Released on: </span>
          <span class="date" data-bind="text: releaseDate"></span>
        </div>
        <div class="title"><span data-bind="text: title"></span></div>
      </div>
    </div>
  </div>
</div>