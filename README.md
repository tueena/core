tueena framework
================
The tueena framework is a combination of a **dependency injection framework**,
an **application bootstrapper** and a class loader. It does not tell you, how to
design your architectore. You can use it to write MVC applications, MVP
application, CLI apps with commands, whatever. It is written in **PHP** and
licensed under the **MIT license**.

Dependency injection ("DI") helps you to write **loose coupled** code and to
write well **testable code**. The tueena framework self is test driven developed
and has a code coverage of 100% (what not means, that it is completely tested of
course).

I focused on **strong error messages**, because I found bad or missing error
messages the biggest problem with other DI frameworks (haven't tried such ones
in php yet).


Installation
------------
Required is PHP >= 4.3.0.

Download the code, clone the repository or use composer (the package name is
`tueena/core`).


How does it work?
-----------------

You write a front controller, that retrieves the application factory. You tell
this factory to create an `Application` object. You run the application. To
build the application, you have to configure the class loader, you have to
define your services (that are all PHP classes, that should be injected later)
and you have to define a main function. This looks something like this:

	<?php

	namespace my\application;

	use tueena\core\Loader;
	use tueena\core\services\ServiceDefinitions;
	use tueena\core\types\Type;
	use ...

	$ApplicationFactory = require_once '/path/to/tueena/framework/source/applicationFactory.php';
	$Application = $ApplicationFactory(

		// Configure the class loader.

		function (Loader $Loader) {
			$Loader->defineNamespaceDirectory('my\\application', __DIR__);
		},

		// Define your services (IHttpRequest, HttpRequest, IRouter and Router are
		// not part of the framework).

		function (ServiceDefinitions $ServiceDefinitions) {
			$ServiceDefinitions
				->add(
					IdentifyingType::is(Type::fromName('IHttpRequest')),
					ImplementingType::is(Type::fromName('HttpRequest'))
				)
				->add(
					IdentifyingType::is(Type::fromName('IRouter')),
					ImplementingType::is(Type::fromName('Router'))
				)
				// ...
				;
		},

		// Your main function (with any parameters, you need).

		function (IRouter $Router, Injetor $Injector, ...)
		{
			// The code here is just an example. You don't have to write
			// routers, controllers, response objects. You can do anything
			// here...

			$Controller = $Router->getControler();
			$methodName = $Router->getControllerMethod();

			$InjectionTarget = new InjectionTargetMethod($Controller, $methodName);
			$Response = $Injector->resolve($InjectionTarget);

			$Response->send();
		}
	);

	$Application->run();

Here now the same file a bit extended and with comments to understand, how it
works and what is possible:

	<?php

	namespace my\application;

	// Those three usings are required.
	use tueena\core\Loader;
	use tueena\core\services\ServiceDefinitions;
	use tueena\core\types\Type;
	use ...

	// The file applicationFactory.php returns the ApplicationFacory closure.

	$ApplicationFactory = require_once '/path/to/tueena/framework/source/applicationFactory.php';

	// This closure is called with three parameters. Each of them has to be
	// a closure. The first one is to configure the class loader, the second one
	// to define your services and the third one is the main function.

	$Application = $ApplicationFactory(

		// Tell the "Loader" where to find your classes.

		// This closure must be defined with one parameter, that will be an
		// instance of \tueena\core\Loader.

		function (Loader $Loader) {

			// The Loader has two public methods, you can use.
			// defineNamespaceDirectory('foo\\bar', '/my/path') means, that
			// a class \foo\bar\baz\Qux would be searched in the file
			// /my/path/baz/Qux.php.
			// With the addLoader() method, you can define a closure, that will
			// be called with the name of the class as parameter. Return true,
			// if you found the file and false, if not.

			$Loader
				->defineNamespaceDirectory('my\\application', __DIR__)
				->addLoader(function ($className) {
					// Include your file and return true on success or
					// false otherwise.
				});

			// The loaders will be called in the order, they have been defined.

		},

		// Define your services.

		// The parameter of this closure must be an instance of
		// \tueena\core\services\ServiceDefinitions.

		function (ServiceDefinitions $ServiceDefinitions) {

			// A service has an identifying type and an implementing type. If
			// the Injector has to resolve a method foo(IBar $Bar, IBaz $Baz),
			// it injects a service with the identifying type IBar and one with
			// the indentifying type IBaz.

			// The identifying type can also be a concrete or abstract class.

			// The implementing type tells the service factory, which class
			// is implementing the service. This implementing class has to be an
			// instanceof the identifying type.

			$ServiceDefinitions
				->add(
					IdentifyingType::is(Type::fromName('IHttpRequest')),
					ImplementingType::is(Type::fromName('HttpRequest'))
				)
				->add(
					IdentifyingType::is(Type::fromName('IRouter')),
					ImplementingType::is(Type::fromName('Router'))
				)

				// The identifying type and the implementing type can also be
				// the same. In this case, you can define the service like this:

				->add(
					IdentifyingType::is(Type::fromName('Users')),
					ImplementingType::isTheSame()
				)

				// You can define a factory function. In this case, you don't
				// define the implementing type.

				->add(
					IdentifyingType::is(Type::fromName('Configuration')),
					FactoryFunction::is(function () {
						return new Configuration(__DIR__ . '/../config.json');
					})
				)

				// The factory function will also be injected with other
				// services, if required.

				->add(
					IdentifyingType::is(Type::fromName('Database')),
					FactoryFunction::is(function (Configuration $Configuration) {
						return new Database(
							$Configuration->get('database.host'),
							$Configuration->get('database.user'),
							$Configuration->get('database.password')
						);
					})
				)

				// If no factory function is defined, the service instance is
				// build by the service factory. If the constructor requires
				// other services, they are injected automatically in this case.

				// You cn also define an init function. This is called after a
				// service has been build (services are build on demand and are
				// only build once, so you'll get always the same instance of a
				// service class).

				->add(
					IdentifyingType::is(Type::fromName('Session')),
					ImplementingType::isTheSame(),
					InitFunction::is(function (Session $Session) {
						$Session->start();
					})
				)

				// In the init function you can also use other services.

				->add(
					IdentifyingType::is(Type::fromName('CurrentUserContainer')),
					ImplementingType::isTheSame(),
					InitFunction::is(function (CurrentUserContainer $CurrentUserContainer, Session $Session, Users $Users) {
						if (!$Users->has('currentUserId'))
							return;
						$currentUserId = $Session->get('currentUserId');
						$CurrentUser = $Users->get(currentUserId);
						$CurrentUserContainer->setUser($CurrentUser);
					})
				);
		},

		// Define the main function.

		// It is called after the services have been defined. Required services
		// will be injected. So you can define any of your defined services
		// as parameters.

		// Two services are defined by the framework: The Loader service (see
		// above) and the Injector. You can use those as well.

		function (IRouter $Router, Injetor $Injector)
		{
			// The code here is just an example. You don't have to write
			// routers, controllers, response ojects. You can do anything
			// here...

			$Controller = $Router->getControler();
			$methodName = $Router->getControllerMethod();

			// We want to call the controller now and want the injector to
			// inject the services into controller, that it needs. This could
			// be the HttpRequest service, the Session, the Configuration, some
			// model repositories, possibly a webservice service and so on).

			// To do this, we have to pass an instance of IInjectionTarget to
			// the resolve() method of the Injector.

			$InjectionTarget = new InjectionTargetMethod($Controller, $methodName);
			$Response = $Injector->resolve($InjectionTarget);

			// Other implementations of IInjectionTarget are
			// InjectionTargetStaticMethod, InjectionTargetConstructor,
			// InjectionTargetInvokeMethod, InjectionTargetClosure and
			// InjectionTargetFunction.

			// Finally send the response or load your views, templates,
			// whatever.

			$Response->send();
		}
	);

	// Now, the application is defined and build. Run it. That's all.

	$Application->run();

And what is that all good for?
------------------------------

You can write nice code now. Code that can easily be tested and is loose
coupled. In a json api application, a controller method to register a new user,
for example, could look like this:

	<?php

	namespace my\application\controllers;

	use my\application\lib\http\HttpRequest;
	use my\application\lib\http\JsonResponseFactory;
	use my\application\CurrentUserContainer;
	use my\application\security\SecurityPolice;
	use my\application\model\users\Users;
	use my\application\model\users\exceptions\UsernameExists;

	class User
	{
		public function register(IHttpRequest $Request, ISecurityPolicy $SecurityPolicy, IUsers $Users, ICurrentUserContainer $CurrentUserContainer, IJsonResponseFactory $JsonResponseFactory)
		{
			$errors = [];

			$password = $Request->getPostParameter('password');
			$passwordErrors = $SecurityPolice->validatePassword($password);
			if (!empty($passwordErrors))
				$errors['password'] = $passwordErrors;

			// ... do some other validation here ...

			$username = $Request->getPostParameter('username');
			if ($Users->hasUsername($username))
				$errors['username'] = ['already-exists'];

			if (!empty($errors))
				return $JsonResponseFactory->createErrorResponse('errors');

			try {
				$NewUser = $Users->add($username, $password, $Request->...);
			} catch (UsernameExists) {
				return $JsonResponseFactory->createErrorResponse([
					'username' => ['already-exists']
 				]);
			}

			$CurrentUserContainer->setUser($NewUser);

			return $JsonResponseFactory->createSuccessResponse($NewUser->getId());
		}
	}

Although there are a lot of other parts of the application required in this
controller method, it is perfectly testable and you don't have to pass
intransparent registries or containers around. In fact you could make this
method static. Or you could for example put the request, the current user
container and the response factory services into a base class `Controller` or
`JsonController`, if you have others, too. I love this way of coding and that's
the reason I wrote this framework.

Feel free to contact me for questions, problems, feedback:  
[bastian.fenske@tueena.org](mailto:bastian.fenske@tueena.org)