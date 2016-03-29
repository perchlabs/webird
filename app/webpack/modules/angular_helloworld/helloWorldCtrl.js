import helloService from './helloWorldService'

export default ['$scope', 'helloWorldService', function($scope, helloService) {
  $scope.msg = {}
  helloService.query().then(function(data) {
    console.log(data)
    $scope.msg = data
  })
}]
