import 'bootstrap/dist/css/bootstrap'
import 'bootstrap/dist/css/bootstrap-theme'
import 'bootstrap/dist/js/bootstrap'
import feature from 'browserfeature'

window.addEventListener('load', function() {
  // Only show the login form when javascript is enabled
  let elOnlyScript = document.getElementById('onlywithscript')
  if (elOnlyScript) {
    elOnlyScript.style.display = ''
  }
})
