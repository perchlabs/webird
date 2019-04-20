import 'commons/init_complex'
import feature from 'browserfeature'

window.addEventListener('load', function() {
  // Only show the login form when javascript is enabled
  let elOnlyScript = document.getElementById('onlywithscript')
  if (elOnlyScript) {
    elOnlyScript.style.display = ''
  }
})
