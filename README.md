# Twig Whitespace Collapse Extension

Cette extension fusionne les espaces, tabulations et sauts de ligne des nœuds texte Twig avant la compilation,
ce qui permet de réduire le poids du contenu généré tout en gardant le code lisible et sans impact sur le temps
d’exécution.

Par exemple le code suivant

```twig
{# base.html.twig #}

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>test</title>
        <link
            rel="stylesheet"
            href="style.css"
        />
    </head>
    <body>
    </body>
</html>

```

sera rendu comme

```html
<!DOCTYPE html>
<html> <head> <meta charset="utf-8" /> <title>test</title> <link rel="stylesheet" href="style.css" /> </head> <body> </body>
</html>
```

## Activation

Cette extension s’ajoute à Twig comme n’importe quelle autre extension.

```php
<?php

$twig->addExtension(new \MatTheCat\Twig\Extension\WhitespaceCollapser());
```

ou si vous êtes sous Symfony par exemple

```yaml
twig.extension.whitespace_collapser:
    class: MatTheCat\Twig\Extension\WhitespaceCollapser
    tags:
        - { name: twig.extension }
```

## Configuration

Le comportement de l’extension dépend du paramètre passé à son constructeur.
Par défaut ce dernier vaut `['html', 'xml', 'svg']`, ce qui active l’extension
pour les templates dont l’extension du fichier est *html*, *html.twig*, *xml*, *xml.twig*,
*svg* ou *svg.twig*.

Vous pouvez donc passer un tableau d’extension ou encore un booléen qui activera l’extension
pour tous les templates, ou la désactivera.

## Tag

L’extension définit également le tag `whitespacecollapse` qui avec un booléen en paramètre permet
d’activer ou désactiver l’extension à l’intérieur d’un template.

Par exemple

```twig
<!DOCTYPE html>
<html>
    <head>
        {% whitespacecollapse false %}
            <meta charset="utf-8" />
            <title>test</title>
            <link
                rel="stylesheet"
                href="style.css"
            />
        {% endwhitespacecollapse %}
    </head>
    <body>
    </body>
</html>
```

sera rendu comme

```html
<!DOCTYPE html>
<html> <head>             <meta charset="utf-8" />
            <title>test</title>
            <link
                rel="stylesheet"
                href="style.css"
            />
         </head> <body> </body>
</html>
```

## :warning: Attention

L’extension fusionne *toute* suite de plus d’un espace, ce qui correspond au cas général
mais peut être destructif dans les cas suivants :

- élément dont la propriété `white-space` vaut `pre`, `pre-wrap` ou `pre-line`
- texte d’éléments de formulaire
- attributs `data-*`

Outre `{% whitespacecollapse false %}`, vous pouvez également rendre le code concerné avec Twig
pour éviter cela. Par exemple

```twig
data-example="{{ 'i  like  spaces' }}"
```