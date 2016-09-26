import $ from 'jquery'
import mousetrap from 'mousetrap'
import highlight from 'highlight'
import 'highlight.js/styles/github'
import './style.less'

export default {
  open,
  close,
  toggle
}

window.wbdebug = this

highlight.registerLanguage('sql', require('highlight.js/lib/languages/sql'))
highlight.registerLanguage('php', require('highlight.js/lib/languages/php'))
highlight.registerLanguage('javascript', require('highlight.js/lib/languages/javascript'))

$('#wbdebug-container').css('display', '')
if (!isSet()) {
  setClosed
}
if (isOpen()) {
  open()
}
mousetrap.bind(['ctrl+shift+d'], function(e) {
  toggle()
})
$('pre code').each(function(i, block) {
  highlight.highlightBlock(block)
})
$("#wbdebug-main-nav a").click(function(e) {
  e.preventDefault()
  let name = $(this).data("open")
  togglePanel(name)
})
$(".wbdebug-panel-close").click(function(e) {
  e.preventDefault()
  closeActivePanel()
})
$(".collapser").click(function() {
  if ($(this).hasClass("closed")) {
    $(this).removeClass("closed")
    $(this).next().show()
  } else {
    $(this).addClass("closed")
    $(this).next().hide()
  }
})

/**
 *
 */
function isOpen() {
  return localStorage.getItem('wbdebug_isOpen') === '1'
}

/**
 *
 */
function isSet() {
  return localStorage.getItem('wbdebug_isOpen') != null
}

/**
 *
 */
function getActivePanel() {
  let activePanel = localStorage.getItem('wbdebug_activePanel')
  return (activePanel) ? activePanel : false
}

/**
 *
 */
function closeActivePanel() {
  localStorage.removeItem('wbdebug_activePanel')
  $(".wbdebug-panel").css('visibility', 'hidden')
  $("#wbdebug-main-nav a").removeClass("active")
}

/**
 *
 */
function openPanel(name) {
  closeActivePanel()
  localStorage.setItem('wbdebug_activePanel', name)

  $("#wbdebug-main-nav a").removeClass("active")
  $(".wbdebug-open[data-open='" + name + "']").addClass("active")

  let $panel = $("#wbdebug-panel-" + name)
  $panel.css('visibility', 'visible')
}

/**
 *
 */
function openActivePanel() {
  let activePanel = getActivePanel()
  if (activePanel !== false) {
    openPanel(activePanel)
  }
}

/**
 *
 */
function togglePanel(name) {
  if (name === getActivePanel()) {
    closeActivePanel()
  } else {
    openPanel(name)
  }
}

/**
 *
 */
function setOpen() {
  localStorage.setItem('wbdebug_isOpen', '1')
}

/**
 *
 */
function setClosed() {
  localStorage.setItem('wbdebug_isOpen', '0')
}

/**
 *
 */
function open() {
  $("#wbdebug-toolbar").css('visibility', 'visible')
  setOpen()
  openActivePanel()
}

/**
 *
 */
function close() {
  $("#wbdebug-toolbar").css('visibility', 'hidden')
  setClosed()
  $(".wbdebug-panel").css('visibility', 'hidden')
}

/**
 *
 */
function toggle() {
  if (isOpen()) {
    close()
  } else {
    open()
  }
}
