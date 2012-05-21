xi-bundle-filebrowser
=====================

Filelib filebrowser for wysiwyg editors

dependencies:
 - filelib *https://github.com/xi-project/xi-filelib 
 - xi-bundle-filelib *https://github.com/xi-project/xi-bundle-filelib
 
## howto setup
1. Make sure you have filelib and xi-bundle-filelib installed
2. Install your wysivyg editor. This pacage is tested to work with TinyMCE but it sould also work with CKEditor
3. Initialize custom browser callback in your js/coffeescript 
4. Edit your config file
5. You might want to restrict access to /file/browser/. Use your project security config file for this.

### 1. install filelib and xi-bundle-filelib
```
Deps -file:
[xi-filelib]
    git=http://github.com/xi-project/xi-filelib.git
    version=master

[XiFilelibBundle]
    git=http://github.com/xi-project/xi-bundle-filelib    
    target=/bundles/Xi/Bundle/FilelibBundle

[XiFilebrowserBundle]
    git@github.com:xi-project/xi-bundle-filebrowser.git
    target=/bundles/Xi/Bundle/Filebrowser
    version=master

```

AppKernel.php
```php
<?php 
  new Xi\Bundle\FilelibBundle\XiFilelibBundle(),
?>
```

### 2. Installing wysivyg editor
You can do this anyway you like. It is possible to use stfalcon TinymceBundle for this *https://github.com/stfalcon/TinymceBundle

if you use stfalcon tinymcebundle be adviced that you must specify your callback in each theme
```yml
    theme:
        simple:
            file_browser_callback: TinyMceFilelibFileBrowserCallback
```

### 3. Initialize custom browser callback in your js/coffeescript 

Following example is in coffeescript format and for TinyMce
```coffeescript

window.TinyMceFilelibFileBrowserCallback = (field_name, url, type, win) ->

    tinyMCE.activeEditor.windowManager.open({
        file : "/file/browser/list/"+ type,
        title : 'File Browser',
        width : 800,  
        height : 800,
        resizable : "yes",
        inline : "yes",  
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });

    return false;

```
### 4. Edit your config file

folder is name of virtual filelib folder where your files are located
```yml
xi_filebrowser:
  folder: filebrowser
```

Update your routing to take account filebrowser 
routing.yml

```yml
XiFilebrowserBundle:
    resource: "@XiFilebrowserBundle/Resources/config/routing.yml"
    prefix:   /
```
