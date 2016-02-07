var app = angular.module('app', ['ngRoute']);

app.config(['$routeProvider', '$locationProvider', '$httpProvider', function($routeProvider, $locationProvider, $httpProvider) {
	$httpProvider.interceptors.push('authInterceptorService');

	$routeProvider
		.when('/', {
			controller: homeController,
			templateUrl: 'app/views/default/home.html'
		})
		.when("/login", {
			controller: loginController,
			templateUrl: 'app/views/auth/login.html'
		}).
		otherwise({
			redirectTo: '/'
		});

	// use the HTML5 History API
	$locationProvider.html5Mode(true);
}]);

app.constant('AppSettings', {
	serviceBase: 'app.php/'
});

app.run(['$rootScope', 'authService', function($rootScope, authService) {
	$rootScope.appName = "Zarb";

	authService.fillAuthData();
}])


app.controller('appController', function() {

});