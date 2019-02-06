# Creating a Multidomain Design

Shopsys Framework provides an ability of running multiple domains as a single application,
if you want to know more about this concept, you can read [the separate article](../introduction/domain-multidomain-multilanguage.md).
This guide shows you, how to distinguish your multiple domains by using custom styles and/or Twig templates.

## Basics
Let us introduce you the design settings in [`domains.yml`](/project-base/app/config/domains.yml) configuration file.
In order to change your multidomain appearance, you can set two parameters here:
- `styles_directory`
    - allows you to define a custom sub-folder with LESS files in `src/Shopsys/ShopBundle/Resources/styles`
    - if you need to use custom styles for a particular domain, put your LESS files in this sub-folder
    - you can create your own directories structure in the sub-folder that suits your needs
- `design_id`
    - allows you to define a design identifier
    - the parameter can be a number (e.g. domain ID), however, you can use a string identifier as well (e.g. "flat-design") so you are able to use the same design across multiple domains
    - if you want to use custom template for a particular domain, duplicate the original one that is used for the first domain and add `.design_id` value as a suffix to its name (e.g. `detail.html.twig` -> `detail.flat-design.html.twig`)
    - all the multi-design templates must be located in the same folders as their originals
        - there is a huge advantage from the usability point of view - when you change a controller, you need to change all the related multi-design templates.
        In such a case, you see all the templates in the same folder and you do not need to seek for them anywhere else

## Model scenarios
### Scenario 1 - I want to use red color for links on my 2nd domain
This is very easy as there are already prepared `less` files for the second domain in `domain2` folder
that is configured for usage by `styles_directory` parameter in [`domains.yml`](/project-base/app/config/domains.yml).

1. Edit `src/Shopsys/ShopBundle/Resources/styles/front/domain2/core/variables.less`:
    ```diff
    - @color-link: @color-green;
    + @color-link: @color-red;
    ```

1. Generate CSS files from LESS using Grunt
    ```
    php phing grunt
    ```

*Notes:*
- *If you are not familiar with LESS and how it deals with file imports, see [the separate article](../frontend/introduction-to-less.md).*
- *If you are not familiar with `phing`, there is [a separate article](../introduction/console-commands-for-application-management-phing-targets.md) about it as well.*

### Scenario 2 - I want to change layout in left panel on my 2nd domain
In the left panel, by default, there is a category tree, a contact form, and an advert box.
Let us say we want to change the elements so the contact form goes first, then the category tree, and the advert box is not there at all.

1. Open [`domains.yml`](/project-base/app/config/domains.yml) and set `design_id` parameter for your 2nd domain.
    ```diff
       domains:
           -   id: 1
               name: shopsys
               locale: en

           -   id: 2
               name: 2.shopsys
               locale: cs
               styles_directory: domain2
    +          design_id: my-design
    ```

1. Duplicate [`layoutWithPanel.html.twig`](/project-base/src/Shopsys/ShopBundle/Resources/views/Front/Layout/layoutWithPanel.html.twig)
and name the new file `layoutWithPanel.my-design.html.twig`. The new file must be in the same folder as the original one.
1. In your new `layoutWithPanel.my-design.html.twig`, re-order the elements in the div element with class `web__main__panel`:
    ```twig
        <div class="web__main__panel">
            <div id="js-contact-form-container">
                {{ render(controller('ShopsysShopBundle:Front/ContactForm:index')) }}
            </div>

            {{ render(controller('ShopsysShopBundle:Front/Category:panel', { request: app.request } )) }}

            {% block panel_content %}{% endblock %}
        </div>
    ```

## Final thoughts
- Since there are two independent parameters for using custom styles and Twig templates,
you are able to combine them arbitrarily to achieve a multidomain design that suits your needs.
E.g. you can have 2 color sets and 3 distinct layouts, and then 6 domains with all the possible combinations.
- It is important to keep in mind that second (and any other than the first) domain is not covered by tests so be aware when using different templates.