# Livescore Mobile Markup

### Main locations

* **config/twig.variables.yml** => Variables used when compiling the Twig templates
* **dist/** => Final directories with the compiled files
* **src/components/livescore** => Custom MDL components
* **src/components/mdl** => Overwriting the official MDL components

### How to compile the files?
First, install the dependencies: `composer install`
Then, compile the whole project: `php application.php compile`

### Real time compilation
You can compile in real time by doing: `php application.php watch`
** NOTE: Make sure `fswatch` is installed on your system.
To install it, you can perform: `brew install fswatch` 

### Others
 
*If you have this error message: Error: Cannot find module 'npmlog'*
Please look at: [GitHub: Cannot find module npmlog](https://github.com/tj/n/issues/101)
