export default [
  '$http', function($http) {
    return {
      query: function() {
        return $http({
          method: 'GET',
          url: '/api/helloworld'
        }).then(function(response) {
          return response.data
        })
      }
    }
  }
]
