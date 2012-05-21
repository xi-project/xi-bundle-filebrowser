window.FileBrowserDialogue =
    init: ->
        elements = document.getElementsByClassName('imagecontainer')
        index = 0
        self = @
        while index < elements.length
            if typeof elements[index] != "undefined"
                elements[index].onclick = () ->
                    self.versionSelector(this)
                    return false
            index++
   
    versionSelector: (element)->
        versions = element.getElementsByClassName('versions')
        versions[0].style.display = "block"
        links = versions[0].getElementsByTagName('a')
        
        self = @
        index = 0
        while index < links.length
            if typeof links[index] != "undefined"
                 links[index].onclick = () ->
                    self.returnValues(this)
                    return false
            index++
 
    returnValues: (element)->
        win = tinyMCEPopup.getWindowArg("window")
        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = element.href
        tinyMCEPopup.close()


tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);