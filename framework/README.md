
DIRECTORY STRUCTURE
-------------------

framework/
	3rdparty/			3rd party library. 
		yii2/			including yii2 framework core classes.
	core/				core and base components.
	exceptions/			difining exception classes.
	exts/				extension classes
	helpers/			static helper function and utility.
	interfaces/			interface difinitions.
	model/				base required database models.
	setup/				framework setup and initial scripts.
	views/				view template files.
		layouts/		layout templates
		widgets/		widget view templates.

	Framework.php		the project referering the framework should include only this file.

REQUIREMENTS
------------

	PHP5.3 or above
	Mysql5.5 or above