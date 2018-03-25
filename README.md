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
    
Route : ``/user/create``
    
Parameters : 
* pseudo
* email
* birthDate[day]
* birthDate[month]
* birthDate[year]
    
###Save user's choice

Save a choice for a user

Route ``/user/{id}/submit-choice``

Parameters :
* id : (route parameter) Id of the user
* film: ImdbId of the film

###Delete Choice

Delete a choice for an user

Route ``/user/{id}/delete/{film}``

Parameters :
* id : (route parameter) Id of the user
* film: (route parameter) ImdbId of the film

###List Choice

List the films choose by the provided user

Route ``/user/{id}/list-choice``

Parameters :
* id : (route parameter) Id of the user

###List Users

List users who choose the provided choice

Route ``/list-users/{choice}``

Parameters :
* choice : (route parameter) ImdbId of the film

###Get Result

Give the most voted film

Route ``/result``

