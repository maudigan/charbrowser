You can put templates in this directory and they will override templates in 
the ./templates/default directory. This is the preffered way to make 
customizations to the front end as these wont be overwritten when you do a 
new install.

It will attempt to use the template from this directory and if not present
it will revert back to the ./templates/default template.