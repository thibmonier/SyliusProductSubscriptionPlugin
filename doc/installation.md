## Installation

1. Run `composer require odiseoteam/sylius-product-subscription-plugin`

2. Enable the plugin in bundles.php

```php
<?php
// config/bundles.php

return [
    // ...
    Odiseo\SyliusProductSubscriptionPlugin\OdiseoSyliusProductSubscriptionPlugin::class => ['all' => true],
];
```

3. Import the plugin configurations

```yml
# config/packages/_sylius.yaml
imports:
    ...

    - { resource: "@OdiseoSyliusProductSubscriptionPlugin/Resources/config/config.yaml" }
```

4. Add the admin routes

```yml
# config/routes.yaml
odiseo_sylius_product_subscription_plugin_admin:
    resource: "@OdiseoSyliusProductSubscriptionPlugin/Resources/config/routing/admin.yaml"
    prefix: /admin
```

5. Include traits and override the models

```php
<?php
// src/Entity/Product/Product.php

// ...
use Doctrine\ORM\Mapping as ORM;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanAwareInterface;
use Odiseo\SyliusProductSubscriptionPlugin\Entity\PlanTrait;
use Sylius\Component\Core\Model\Product as BaseProduct;

/**
 * @ORM\Table(name="sylius_product")
 * @ORM\Entity
 */
class Product extends BaseProduct implements PlanAwareInterface
{
    use PlanTrait;

    // ...
}
```

```yml
# config/packages/_sylius.yaml
sylius_product:
    resources:
        product:
            classes:
                model: App\Entity\Product
```

6. Add the plan select box to the product form edit page. So, you need to run `mkdir -p templates/bundles/SyliusAdminBundle/Product/Tab` then `cp vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Resources/views/Product/Tab/_details.html.twig templates/bundles/SyliusAdminBundle/Product/Tab/_details.html.twig` and then add the form widget

```twig
{# ... #}
{{ form_row(form.code) }}
{{ form_row(form.enabled) }}
{{ form_row(form.plan) }}
{# ... #}
```

7. Finish the installation updating the database schema and installing assets

```
php bin/console doctrine:schema:update --force
php bin/console sylius:theme:assets:install
```
