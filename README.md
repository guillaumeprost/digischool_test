Test Digischool
============

## Configuration

Configure your database in .env

Create database :
``php bin/console doctrine:database:create``

Update database schema :
``php bin/console doctrine:schema:update``

API key imdb `28a104cc`

## API

###Create User
    
Route : ``/user`` (POST)
    
Parameters : 
* pseudo
* email
* birthDate (format: yyyy-MM-dd)
    
###Save user's choice

Save a choice for a user

Route: ``/choice`` (POST)

Parameters :
* userId : (query parameter) Id of the user
* film: ImdbId of the film

###Delete Choice

Delete a choice for an user

Route ``/choice/{imdbId}`` (DELETE)

Parameters :
* userId : (query parameter) Id of the user
* imdbId: (route parameter) ImdbId of the film

###List Choice

List the films choose by the provided user

Route ``/choices``

Parameters :
* userId : (query parameter) Id of the user

###List Users

List users who choose the provided choice

Route ``/users``

Parameters :
* choice : (query parameter) ImdbId of the film

###Get Result

Give the most voted film

Route ``/result``

