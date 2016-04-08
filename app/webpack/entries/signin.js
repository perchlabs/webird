'use strict'
import feature from 'browserfeature'

window.onload = function() {
  // Only show the login form when javascript is enabled
  let elOnlyScript = document.getElementById('onlywithscript')
  if (elOnlyScript) {
    elOnlyScript.style.display = ''
  }
}
