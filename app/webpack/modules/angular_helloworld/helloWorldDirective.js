import helloWorldCtrl from './helloWorldCtrl'
import template from './partials/helloWorld'

export default function() {
  return {
    template,
    controller: helloWorldCtrl,
    restrict: 'A'
  }
}
