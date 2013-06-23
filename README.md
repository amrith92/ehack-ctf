ehack-ctf
=========

EHack CTF Event

This is the official repo for the ehack-ctf event.

Procedure
=========

1. Simply run composer to install all dependencies
2. Copy the following `parameters.yml` and replace appropriately

```yaml
	parameters:
	    database_driver: pdo_mysql
	    database_host: 127.0.0.1
	    database_port: '3306'
	    database_name: 
	    database_user: 
	    database_password: 
	    mailer_transport: smtp
	    mailer_host: 127.0.0.1
	    mailer_user: null
	    mailer_password: null
	    locale: en
	    secret: dab4148f21d1070312ebddd97ba4339a5
	    database_path: null
	    max_per_team: 5
	    key: ehacencrykeandecrypdo33659198501
	    iv: thfirske48130729
	    facebook_client_secret: 
	    facebook_client_id: 
	    google_client_id: 
	    google_client_secret: 
	    twitter_client_id: 	
	    twitter_client_secret: 
```

3. Enjoy the show.

Admin
=====

There are quite a few TODOs. To access the admin panel, look in the
`config.yml` file. There's a (temporary) in-memory DB of admin-users.

The official admin is:

```yml
	username: admin
	password: PASS_word
```

Dynamic Questions
=================

There is quite a flexible framework for questions with unique answers.
The Admin is required to enter the Answer Template in a specific format.
BBCODE is used to denote dynamic sections, where you can specify the
properties of the game state you need to calculate an answer.

```
	[ddynamic]
	  [params]
	    # Property List
	  [/params]

	  # Code
	[/ddynamic]

	# Result
```

 * Property List
	This should be comma-separated list of valid properties.
	Must *not* contain any whitespaces between properties.
 * Code
	Code should be valid PHP code. Closures are allowed.
	Standard PHP functions allowed. All variables must
	be explicitly defined *other than those already
	defined in the property list!*
 * Result
	This value will be compared with the calculated result
	(as calculated by the Code). See example.

The various properties (currently) available are:

```yml
	name:       Full Name
	firstname:  First Name
	lastname:   Last Name
	id:         User Id
	teamname:   Team Name
	teamid:     Team Id
	answer:     User's current answer
	number:     User's phone-number (as entered)
```

Imagine that the User is expected to enter the MD5 hash of his first name
as the answer. The following code will serve as the required answer-template
to validate it sucessfully.

```
[ddynamic]
  [params]
     firstname,answer
  [/params]

  return (\md5($firstname) == $answer;
[/ddynamic]

1
```

User Panel & Interface
======================

Currently on a provisional basis.

