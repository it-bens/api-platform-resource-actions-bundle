# The API Platform Resource Action Bundle

![Maintenance Status](https://img.shields.io/badge/Maintained%3F-yes-green.svg)
[![Tests](https://github.com/it-bens/api-platform-resource-actions-bundle/actions/workflows/test.yml/badge.svg?branch=master)](https://github.com/it-bens/api-platform-resource-actions-bundle/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/it-bens/api-platform-resource-actions-bundle/branch/master/graph/badge.svg?token=P3RZM11FNV)](https://codecov.io/gh/it-bens/api-platform-resource-actions-bundle)

## Motivation
### Let's start with Symfony & API Platform :construction_worker:
Symfony & API Platform are a great combination to easily create a REST API for a CRUD application.
The flexibility of a REST API is limited by design. Because the operations are generic, it's difficult to depict more specific operations.

Typical ways of updating a models are to apply a full update operation (REST PUT) or to apply partial updates (REST PATCH).
Often model updates are connected to certain requirements or side effects. The implementation of them often can become very complex.

One way to solve this problem is to use commands objects that are applied to the model. 
The command implementations can be separated to check for requirements or to trigger side effects without writing longer and longer methods.
Unfortunately, this concept collides with the simplicity of REST APIs implemented with API Platform. Custom operations can be implemented,
but this results in a lot of boilerplate code. API Platform could also populate a generic update-DTO and dispatch it via bus.
But the handler of this DTO would have to create the command objects and apply them. This would require a more or less complex logic,
which is difficult to maintain and test.

### API Platform resource actions to the rescue! :superhero:
In a REST API context the mentioned 'models' can be called 'resources'. An action refers to the mentioned commands but is more general.

This bundle provides the ability to attach actions to an API Platform resource operation via configuration.
Internal a generic update DTO is used and later unpacked to create the desired action. After processing, the action object is dispatched via bus.
It can be handled from there like it came from any other source like form request or a console command.
This helps to keep API-logic-code out of your models and make them more agnostic about their data source.

## Installation
The bundle can be installed via Composer:
```bash
composer require it-bens/api-platform-resource-actions-bundle
```
If you're using Symfony Flex, the bundle will be automatically enabled (but has to be configured manually). 
For older apps, enable it in your Kernel class.

## Action configuration
To register an action, three things are required: a DTO, a configuration entry and a proper resource configuration in API Platform.

Let's assume there is a command DTO like this:
```php
namespace TheNamespace;

class AppendToPropertyOne {
    public function __construct(
        private TheNamespace\TheEntity $entity, 
        private string $toAppend
    ) {}
}
```
The command contains the entity it will be applied on and one more property.

The actions can be defined (and registered) via bundle configuration and/or class attributes.
Both sources can be combined, but an exception will be thrown if an action is defined more than once for a resource.

The bundle will check if the resources, used in the definitions, are registered in API Platform.

### Action definition with configuration files
```yaml
itb_api_platform_resource_actions:
   resources:
      TheNamespace\TheEntity:
         increase-property-two:
            command_class: TheNamespace\AppendToPropertyOne
            description: Appends a string to property one.
```
The `resource` key refers to the API Platform resources.
The sub-key represents the action name (it will not be normalized into snake-case).
The description is optional and will be used for the OpenAPI documentation (blank descriptions result in exceptions).

### Action definition with attributes
```php
namespace TheNamespace;

use ApiPlatform\Core\Annotation\ApiResource;
use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;

#[ApiResource]
#[ResourceAction(actionName: 'increase-property-two', commandClass: AppendToPropertyOne::class, description: 'Appends a string to property one.')]
class TheEntity {
    ...
}
```
The `description` parameter is optional and can also be null.

Normally this bundle will search for the attribute in all registered classes. The considered classes can be restricted by their namespace.
```yaml
itb_api_platform_resource_actions:
  resources:
    TheNamespace\TheEntity:
      increase-property-two:
        command_class: TheNamespace\AppendToPropertyOne
        description: Appends a string to property one.
```

### Configuration of API Platform
So far this bundle won't do anything (except registering some services).
The actions can be used after an API Platform operation is configured to use the controller and the request DTO provided by this bundle.
Sub-Namespaces are considered as well.
```yaml
itb_api_platform_resource_actions:
   attribute_namespaces: [ TheNamespace ]
```

Of course the configuration can be done via attributes as well.
```php
use ITB\ApiPlatformResourceActionsBundle\Request\Request;
use ITB\ApiPlatformResourceActionsBundle\Controller\Controller;
use ITB\ApiPlatformResourceActionsBundle\Attribute\ResourceAction;

#[ApiResource(itemOperations: [
    'patch' => [
        'input': Request::class,
        'controller': Controller::class
    ]
])]
#[ResourceAction(...)]
class TheEntity {
    ...
}
```
> :information_source: The `messenger` option has to be enabled, if the command should be handled there. Custom data persisters could be used as well.

### Listing of configured actions via console
After configuration, the resource actions can be displayed via Symfony console and filtered by a resource.
```bash
php bin/console itb:api-platform-resource-actions:list-actions
# or with filter by resource
php bin/console itb:api-platform-resource-actions:list-actions --resource="TheNamespace\TheEntity"
```
The console will display a table with the columns "API Platform resource", "action name", "command class" and "description".

## Action validation
This bundle can validate the created command manually. API Platform validates the input, but as this stage, the input object is generic.
That's why the command is validated in the controller. It's disabled by default and can be enabled in the bundle configuration.
```yaml
itb_api_platform_resource_actions:
  validate_command: true
```

## The Open API documentation
| !["The code is documentation enough" button](docs/images/the-code-is-documentation-enough.png?raw=true "The code is documentation enough - button") |
| :--: |
| *Image provided by GetDigital (https://www.getdigital.de/geek-button-the-code-is-documentation-enough.html)* |

Well NO! But here: maybe? :thinking:

API Platform can automatically create an OpenAPI documentation. This bundle hooks into this process via decoration
like described here: https://api-platform.com/docs/core/openapi/. 

It will add a table to the operation description like this:

![OpenAPI documentation for operation with actions](docs/images/action-operation-with-documentation.png?raw=true "OpenAPI documentation")

The `Payload` column shows the properties of the command class. 
If the class contains a property that has the same type as the API Platform resource, it will be removed from the list
(because this is most likely the object, the command will be applied on).

## Internal process
The process is closely coupled to API Platform and contains several steps:
1. API Platform denormalizes the raw data into the generic `Request` DTO of this bundle.
2. API Platform calls the `RequestTransformer` as a data transformer.
   It injects the resource class and the resource object into the payload of the `Request` object.
3. API Platform validates the `Request` object with the `ActionRequestValidator`.
   It checks if the action is registered for the resource and if the payload contains the necessary data by using the `CommandFactory`.
4. API Platform/The router calls the `Controller` and passes the `Request` object to it.
5. The controller denormalizes the payload data into the command.
6. The controller validates the command with the API Platform validator (if enabled).
7. The controller returns the command to the default API Platform flow.

## Current Limitations
### Commands with two properties of the resource type
This bundle can handle the creation of a command that has two properties with the type of the related resources.
It uses the ITB `ReflectionConstructor` (https://github.com/it-bens/reflection-constructor),
which can use a list of ignored property names when injecting the resource object into the payload for later denormalization.

There can be problems with this process if not all class properties are required or set by the constructor.
(which would be bad practise anyway).

Furthermore, the OpenAPI documentation of the payload properties ignores any property that has the same type as the resource.
In the case of two properties of that type, the bundle itself would work (like described) but the documentation would be incomplete.

### Double Denormalization
The `Request` class is validated as a generic class by API Platform. 
The `ActionRequestValidator` tries to denormalize the payload to the command object and catches denormalization exceptions.
If that validation passes, the denormalization is done again in the controller. 
To minimize the performance impact of this double denormalization, a serializer cache should be used.

### Commands without constructor
The `RequestTransformer` will look for a constructor argument that has the type of the resource.
If the command / DTO contains no constructor, it is assumed that the command requires no resource object.

This bundle uses the default Symfony serializer (created by the framework) for the denormalization.
Therefore, this process is limited to the capabilities of the Symfony serializer(s).

### Multiple operations with actions per resource
The operation for an action is identified by the resource configuration of API Platform.
It is assumed that an operation (of a specified resource) will use these actions if the 'input' key is set to `Request` 
and the controller is set to `Controller` (of this bundle).
If two or more operations of the same resource are configured like this, only the first one will be used.

The controller would still be called by API Platform for any other operation, and it would fail to find its corresponding actions.

## Related Packages/Bundles
Because API Platform will always use the default bus to dispatch the command as a message, the usage of the
Message Bus Redirect Bundle (https://github.com/it-bens/message-bus-redirect-bundle) comes in handy.

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive)
and every pull request that provides other or even better code to this package.

You are all breathtaking!