'use strict'
# system
$ = require 'jquery'
mousetrap = require 'mousetrap'
Highlight = require 'highlight'
require 'highlight.js/styles/github'
# local
require './style.less'

highlight = new Highlight()
highlight.registerLanguage('sql', require('highlight.js/lib/languages/sql'))
highlight.registerLanguage('php', require('highlight.js/lib/languages/php'))
highlight.registerLanguage('javascript', require('highlight.js/lib/languages/javascript'))
highlight.registerLanguage('coffeescript', require('highlight.js/lib/languages/coffeescript'))

isOpen = -> localStorage.getItem('wbdebug_isOpen') is '1'
isSet = -> localStorage.getItem('wbdebug_isOpen')?


getActivePanel = ->
  activePanel = localStorage.getItem 'wbdebug_activePanel'
  return if activePanel? then activePanel else false


closeActivePanel = ->
  localStorage.removeItem 'wbdebug_activePanel'
  $(".wbdebug-panel").css('visibility', 'hidden')
  $("#wbdebug-main-nav a").removeClass "active"


openPanel = (name) ->
  closeActivePanel()
  localStorage.setItem 'wbdebug_activePanel', name

  $("#wbdebug-main-nav a").removeClass "active"
  $(".wbdebug-open[data-open='#{name}']").addClass "active"

  $panel = $("#wbdebug-panel-#{name}")
  $panel.css('visibility', 'visible')


openActivePanel = ->
  activePanel = getActivePanel()
  if activePanel isnt false then openPanel activePanel


togglePanel = (name) ->
  if name is getActivePanel()
    closeActivePanel()
  else
    openPanel name


setOpen = ->
  localStorage.setItem 'wbdebug_isOpen', '1'
  return


setClosed = ->
  localStorage.setItem 'wbdebug_isOpen', '0'
  return


open = ->
  $("#wbdebug-toolbar").css('visibility', 'visible')
  setOpen()
  openActivePanel()
  return


close = ->
  $("#wbdebug-toolbar").css('visibility', 'hidden')
  setClosed()
  $(".wbdebug-panel").css('visibility', 'hidden')
  return


toggle = ->
  if isOpen() then close() else open()
  return


init = ->
  $('#wbdebug-container').css('display', '')

  setClosed if not isSet()
  open() if isOpen()

  mousetrap.bind ['ctrl+shift+d', 'command+shift+d'], (e) ->
    toggle()

  $('pre code').each (i, block) ->
    highlight.highlightBlock block

  # SHOW/HIDE MAIN PANELS, ADD ACTIVE CSS CLASS TO SELECTED NAV LINK
  $("#wbdebug-main-nav a").click (e) ->
    e.preventDefault()
    name = $(this).data("open")
    # name = name.replace("#", "")
    # openPanel name
    togglePanel name
    return


  # CLOSE PANEL/REMOVE ACTIVE CLASS FROM NAV LINK
  $(".wbdebug-panel-close").click (e) ->
    e.preventDefault()
    closeActivePanel()
    return


  # Collapsers
  $(".collapser").click ->
    if $(this).hasClass("closed")
      $(this).removeClass "closed"
      $(this).next().show()
    else
      $(this).addClass "closed"
      $(this).next().hide()
    return

  return


module.exports =
  open: open
  close: close
  init: init
  toggle: toggle

window.wbdebug = module.exports
