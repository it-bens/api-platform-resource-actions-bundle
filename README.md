# The API Platform Update Action Bundle

API Platform provides an easy way to create a REST API for entities and models in a project. REST APIs are especially useful for CRUD applications. 
Traditionally, the PUT item operation is used to update all properties of a resource and the PATCH operation is used for partial updates.

However, generic update operations are not always useful or appropriate.
Defined commands are a good way to update the data of a resource (entity/model or whatever). 
They can be validated, handled via messenger and the business logic is encapsulated in the resource that the command is applied on.

API Platform can use such commands objects as input DTOs, but it gets a little tedious when it comes to updates.
Normally, every update operation with a command would require a custom operation (maybe including a request transformer and a custom controller).

This bundle provides an easy way to allow defined resource updates via command with only one operation (e.g. PATCH).
In the process the command object is created, it can be validated automatically (or not) and sent to the message bus for handling.

> ⚠ The bundle is currently not tested by any unit or functional tests! It is used in non-public projects and works so far.

## How to install the bundle?
The bundle can be installed via Composer:
```bash
composer require it-bens/api-platform-update-actions-bundle
```
If you're using Symfony Flex, the bundle will be automatically enabled (but has to be configured manually). 
For older apps, enable it in your Kernel class.

## How are the actions registered?
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

The action itself can be registered via configuration:
```yaml
itb_api_platform_update_actions:
  resources:
    TheNamespace\TheEntity:
      increase-property-two:
        command_class: TheNamespace\AppendToPropertyOne
        description: Appends a string to property one.
```
The `resource` key refers to the API Platform resources. The bundle will check if the resource is registered in API Platform.
The sub-key represents the action name (it will not be normalized into snake-case). 
The description is optional and will be used for the OpenAPI documentation (blank descriptions result in exceptions).

So far this bundle won't do anything (except registering some services). 
The actions can be used after an API Platform operation is configured to use the controller and the request DTO provided by this bundle.
```yaml
TheNamespace\TheEntity:
  itemOperations:
    patch:
      input: ITB\ApiPlatformUpdateActionsBundle\Request\Request
      controller: ITB\ApiPlatformUpdateActionsBundle\Controller\Controller
      openapi_context:
        summary: Updates the entity with defined actions.
```

## How does the overall process work?
The process is closely coupled to API Platform and contains several steps:
1. API Platform denormalizes the raw data into the generic `Request` DTO of this bundle.
2. API Platform calls the `RequestTransformer` as a data transformer. 
It injects the resource class and the resource object into the payload of the `Request` object.
3. API Platform validates the `Request` object with the `UpdateRequestValidator`.
It checks if the action is registered for the resource and if the payload contains the necessary data by using the `CommandFactory`.
4. API Platform/The router calls the `Controller` and passes the `Request` object to it.
5. The controller denormalizes the payload data into the command.
6. The controller validates the command with the API Platform validator (if enabled).
7. The controller dispatches the command into the default bus and returns the result
(and converts any validation exception into an API Platform validation constraint violation).

## What about the validation?
As stated before, this bundle uses the Symfony messenger to dispatch the command as a message.
Therefor validation can be done in two ways: explicitly in the controller or with a message bus middleware.
Both can be turned on and off via configuration:
```yaml
itb_api_platform_update_actions:
  validate_command: false
  ignore_messenger_validation: false
```
The `validate_command` key determines if the API Platform validator is used to validate the command explicitly.
The `ignore_messenger_validation` key determines if a validation exception, thrown by the message bus, 
is caught and turned into an API Platform constraint violation exception (`ignore_messenger_validation = false`)
or will be passed through (`ignore_messenger_validation = true`).

## The Open API documentation
As we know, the code is not documentation enough!

API Platform can automatically create an OpenAPI documentation. This bundle hooks into this process via decoration
like described here: https://api-platform.com/docs/core/openapi/. 

It will add a table to the operation description like this:

![OpenAPI documentation for operation with actions](docs/images/action-operation-with-documentation.png?raw=true "OpenAPI documentation")

The `Payload` column shows the properties of the command class. 
If the class contains a property that has the same type as the API Platform resource, it will be removed from the list
(because this is most likely the object, the command will be applied on).

## Current Limitations of this bundle
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
The `UpdateRequestValidator` tries to denormalize the payload to the command object and catches denormalization exceptions.
If that validation passes, the denormalization is done again in the controller. 
To minimize the performance impact of this double denormalization, a serializer cache should be used.

## Related Packages/Bundles
Because the `Controller` will use the default bus to dispatch the command as a message, the usage of the
Message Bus Redirect Bundle (https://github.com/it-bens/message-bus-redirect-bundle) comes in handy.

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive)
and every pull request that provides other or even better code to this package.

You are all breathtaking!