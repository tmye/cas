<?php

/* base.html.twig */
class __TwigTemplate_874f6ab2995e853d806b4db015ebb02b6cb6e43d19d34764146b6f744b99cf43 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_47e808e9b1cacf5c7c96d8d04e55c8cbf51c0fd3cc93bf2a4367caf74f1fbbda = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_47e808e9b1cacf5c7c96d8d04e55c8cbf51c0fd3cc93bf2a4367caf74f1fbbda->enter($__internal_47e808e9b1cacf5c7c96d8d04e55c8cbf51c0fd3cc93bf2a4367caf74f1fbbda_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>

        <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css\">
        <link rel=\"stylesheet\" href=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("css/style.css"), "html", null, true);
        echo "\">
        ";
        // line 9
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 10
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    </head>
    <body>
    <div class=\"row\">
        <div class=\"col l2\">
            <ul id=\"nav-mobile\" class=\"side-nav fixed\">
                <li class=\"center\" style=\"background-image: url(";
        // line 16
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/background_spot.jpg"), "html", null, true);
        echo ");padding-top: 20px\">
                    <img src=\"";
        // line 17
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/avatar.jpg"), "html", null, true);
        echo "\" alt=\"avatar\" class=\"circle\">
                    <p class=\"p-greeting white-text\">Bonjour Mr Smith</p>
                    <p class=\"white-text\">Votre license expire le 31/01/2018</p>
                </li>
                <br>
                <div class=\"row\">
                    <div class=\"col l10 offset-l1\">
                        <ul class=\"collapsible\" data-collapsible=\"accordion\">
                            <li>
                                <div class=\"collapsible-header active valign-wrapper\">
                                    <span class=\"fa fa-home\"></span>
                                    <a href=\"";
        // line 28
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("homepage");
        echo "\" style=\"margin-left: 4px;color: inherit\">Accueil</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-file-text-o\"></span>
                                    <span style=\"margin-left: 4px\">Présences</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"";
        // line 37
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("historique");
        echo "\" class=\"collapsible-links\">Historiques</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-line-chart\"></span>
                                    <span style=\"margin-left: 4px\">Statistiques</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"";
        // line 46
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("viewDepStat");
        echo "\" class=\"collapsible-links\">Départements</a>
                                    <br>
                                    <a href=\"";
        // line 48
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("viewPersStat");
        echo "\" class=\"collapsible-links\">Personnelles</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-file\"></span>
                                    <span style=\"margin-left: 4px\">Permissions</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"#\" class=\"collapsible-links\">Liste</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Demander</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header active valign-wrapper\">
                                    <span class=\"fa fa-home\"></span>
                                    <a href=\"#\" style=\"margin-left: 4px;color: inherit\">Départements</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-user\"></span>
                                    <span style=\"margin-left: 4px\">Employés</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"";
        // line 74
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("addEmployee");
        echo "\" class=\"collapsible-links\">Ajouter</a>
                                    <br>
                                    <a href=\"";
        // line 76
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("viewEmployee");
        echo "\" class=\"collapsible-links\">Informations</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-wrench\"></span>
                                    <span style=\"margin-left: 4px\">Réglages</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"";
        // line 85
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("imageVeille");
        echo "\" class=\"collapsible-links\">Images de veille</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Heures de travails</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Machines enrégistrées</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Synchroniser</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>
        <div>
            <nav class=\"teal col l10\" style=\"background-image: url(";
        // line 100
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/background_spot.jpg"), "html", null, true);
        echo ")\">
                <div class=\"row\">
                    <div class=\"col l11 offset-l1\">
                        <div class=\"nav-wrapper\">
                            <a href=\"#\" class=\"brand-logo\">

                                <!-- <button class=\"hide-on-large-only\"> btn </button> -->
                                <span>logo c.a.s</span>
                            </a>
                            <!-- Structure du menu deroulant au cas ou on serait sur mobile -->

                            <ul class=\"side-nav\" id=\"mobile-demo\">

                                <?php
                            include \"includes/side-nav.html\";
                            ?>
                                <li><a href=\"#\">Mon compte</a></li>
                                <li><a href=\"#\">Déconnexion</a></li>
                            </ul>
                            <a href=\"#\" data-activates=\"mobile-demo\" class=\"button-collapse\"><i class=\"material-icons\">menu</i></a>

                            <ul id=\"nav-mobile\" class=\"right hide-on-med-and-down\">
                                <li style=\"margin-right: 20px\">
                                <span href=\"#\" class=\"black-links valign-wrapper\">
                                    <img src=\"";
        // line 124
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("img/arm2.png"), "html", null, true);
        echo "\" alt=\"armoirie\" class=\"circle img-armoirie\">
                                    <span class=\"white-text\" style=\"margin-left: 5px\">Ministère des Postes et de l'Economie Numérique</span>
                                </span>
                                </li>

                                <!-- Structure du menu deroulant -->

                                <ul id=\"menu-deroulant\" class=\"dropdown-content\">
                                    <li class=\"valign-wrapper\">
                                        <span class=\"icon-user-md\"></span>
                                        <a href=\"#!\">Mon compte</a>
                                    </li>
                                    <li class=\"divider\"></li>
                                    <li class=\"valign-wrapper\">
                                        <span class=\"icon-off\"></span>
                                        <a href=\"#!\">Déconnexion</a>
                                    </li>
                                </ul>
                                <li><a href=\"#\" class=\"dropdown-button\" data-activates=\"menu-deroulant\"><span class=\"icon-user\"></span> John Smith</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
        ";
        // line 150
        $this->displayBlock('body', $context, $blocks);
        // line 151
        echo "
    <script
            src=\"https://code.jquery.com/jquery-3.3.1.min.js\"
            integrity=\"sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=\"
            crossorigin=\"anonymous\">

    </script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js\"></script>
    <script src=\"";
        // line 159
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("js/script.js"), "html", null, true);
        echo "\"></script>
    <script src=\"https://use.fontawesome.com/4f517cd7b4.js\"></script>
    ";
        // line 161
        $this->displayBlock('javascripts', $context, $blocks);
        // line 162
        echo "    </body>
</html>
";
        
        $__internal_47e808e9b1cacf5c7c96d8d04e55c8cbf51c0fd3cc93bf2a4367caf74f1fbbda->leave($__internal_47e808e9b1cacf5c7c96d8d04e55c8cbf51c0fd3cc93bf2a4367caf74f1fbbda_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_ac847833706f840c7563a30801d021a9ef6ce6a23f9710ba8a46e93dd91db9f3 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_ac847833706f840c7563a30801d021a9ef6ce6a23f9710ba8a46e93dd91db9f3->enter($__internal_ac847833706f840c7563a30801d021a9ef6ce6a23f9710ba8a46e93dd91db9f3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo "C.A.S - ";
        
        $__internal_ac847833706f840c7563a30801d021a9ef6ce6a23f9710ba8a46e93dd91db9f3->leave($__internal_ac847833706f840c7563a30801d021a9ef6ce6a23f9710ba8a46e93dd91db9f3_prof);

    }

    // line 9
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_4fe59ec260717cdfc8e38e743c817bfa1925efc8721128ece5def90294816ff9 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_4fe59ec260717cdfc8e38e743c817bfa1925efc8721128ece5def90294816ff9->enter($__internal_4fe59ec260717cdfc8e38e743c817bfa1925efc8721128ece5def90294816ff9_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_4fe59ec260717cdfc8e38e743c817bfa1925efc8721128ece5def90294816ff9->leave($__internal_4fe59ec260717cdfc8e38e743c817bfa1925efc8721128ece5def90294816ff9_prof);

    }

    // line 150
    public function block_body($context, array $blocks = array())
    {
        $__internal_97ada5f91748815f90897b98f37b547770ccd0ee68157bb51fd9c1ca58083340 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_97ada5f91748815f90897b98f37b547770ccd0ee68157bb51fd9c1ca58083340->enter($__internal_97ada5f91748815f90897b98f37b547770ccd0ee68157bb51fd9c1ca58083340_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_97ada5f91748815f90897b98f37b547770ccd0ee68157bb51fd9c1ca58083340->leave($__internal_97ada5f91748815f90897b98f37b547770ccd0ee68157bb51fd9c1ca58083340_prof);

    }

    // line 161
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_3e10df4d252d0fd270a6181b37c79e1321707c28885263dea629d4f9c56e0151 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_3e10df4d252d0fd270a6181b37c79e1321707c28885263dea629d4f9c56e0151->enter($__internal_3e10df4d252d0fd270a6181b37c79e1321707c28885263dea629d4f9c56e0151_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_3e10df4d252d0fd270a6181b37c79e1321707c28885263dea629d4f9c56e0151->leave($__internal_3e10df4d252d0fd270a6181b37c79e1321707c28885263dea629d4f9c56e0151_prof);

    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  283 => 161,  272 => 150,  261 => 9,  249 => 5,  240 => 162,  238 => 161,  233 => 159,  223 => 151,  221 => 150,  192 => 124,  165 => 100,  147 => 85,  135 => 76,  130 => 74,  101 => 48,  96 => 46,  84 => 37,  72 => 28,  58 => 17,  54 => 16,  44 => 10,  42 => 9,  38 => 8,  32 => 5,  26 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>{% block title %}C.A.S - {% endblock %}</title>

        <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css\">
        <link rel=\"stylesheet\" href=\"{{ asset('css/style.css') }}\">
        {% block stylesheets %}{% endblock %}
        <link rel=\"icon\" type=\"image/x-icon\" href=\"{{ asset('favicon.ico') }}\" />
    </head>
    <body>
    <div class=\"row\">
        <div class=\"col l2\">
            <ul id=\"nav-mobile\" class=\"side-nav fixed\">
                <li class=\"center\" style=\"background-image: url({{ asset('img/background_spot.jpg') }});padding-top: 20px\">
                    <img src=\"{{ asset('img/avatar.jpg')}}\" alt=\"avatar\" class=\"circle\">
                    <p class=\"p-greeting white-text\">Bonjour Mr Smith</p>
                    <p class=\"white-text\">Votre license expire le 31/01/2018</p>
                </li>
                <br>
                <div class=\"row\">
                    <div class=\"col l10 offset-l1\">
                        <ul class=\"collapsible\" data-collapsible=\"accordion\">
                            <li>
                                <div class=\"collapsible-header active valign-wrapper\">
                                    <span class=\"fa fa-home\"></span>
                                    <a href=\"{{ path('homepage') }}\" style=\"margin-left: 4px;color: inherit\">Accueil</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-file-text-o\"></span>
                                    <span style=\"margin-left: 4px\">Présences</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"{{ path(\"historique\") }}\" class=\"collapsible-links\">Historiques</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-line-chart\"></span>
                                    <span style=\"margin-left: 4px\">Statistiques</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"{{ path(\"viewDepStat\") }}\" class=\"collapsible-links\">Départements</a>
                                    <br>
                                    <a href=\"{{ path(\"viewPersStat\") }}\" class=\"collapsible-links\">Personnelles</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-file\"></span>
                                    <span style=\"margin-left: 4px\">Permissions</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"#\" class=\"collapsible-links\">Liste</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Demander</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header active valign-wrapper\">
                                    <span class=\"fa fa-home\"></span>
                                    <a href=\"#\" style=\"margin-left: 4px;color: inherit\">Départements</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-user\"></span>
                                    <span style=\"margin-left: 4px\">Employés</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"{{ path(\"addEmployee\") }}\" class=\"collapsible-links\">Ajouter</a>
                                    <br>
                                    <a href=\"{{ path(\"viewEmployee\") }}\" class=\"collapsible-links\">Informations</a>
                                </div>
                            </li>
                            <li>
                                <div class=\"collapsible-header valign-wrapper\">
                                    <span class=\"fa fa-wrench\"></span>
                                    <span style=\"margin-left: 4px\">Réglages</span>
                                </div>
                                <div class=\"collapsible-body nav-collapsible-body\">
                                    <a href=\"{{ path(\"imageVeille\") }}\" class=\"collapsible-links\">Images de veille</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Heures de travails</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Machines enrégistrées</a>
                                    <br>
                                    <a href=\"#\" class=\"collapsible-links\">Synchroniser</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </ul>
        </div>
        <div>
            <nav class=\"teal col l10\" style=\"background-image: url({{ asset('img/background_spot.jpg') }})\">
                <div class=\"row\">
                    <div class=\"col l11 offset-l1\">
                        <div class=\"nav-wrapper\">
                            <a href=\"#\" class=\"brand-logo\">

                                <!-- <button class=\"hide-on-large-only\"> btn </button> -->
                                <span>logo c.a.s</span>
                            </a>
                            <!-- Structure du menu deroulant au cas ou on serait sur mobile -->

                            <ul class=\"side-nav\" id=\"mobile-demo\">

                                <?php
                            include \"includes/side-nav.html\";
                            ?>
                                <li><a href=\"#\">Mon compte</a></li>
                                <li><a href=\"#\">Déconnexion</a></li>
                            </ul>
                            <a href=\"#\" data-activates=\"mobile-demo\" class=\"button-collapse\"><i class=\"material-icons\">menu</i></a>

                            <ul id=\"nav-mobile\" class=\"right hide-on-med-and-down\">
                                <li style=\"margin-right: 20px\">
                                <span href=\"#\" class=\"black-links valign-wrapper\">
                                    <img src=\"{{ asset('img/arm2.png') }}\" alt=\"armoirie\" class=\"circle img-armoirie\">
                                    <span class=\"white-text\" style=\"margin-left: 5px\">Ministère des Postes et de l'Economie Numérique</span>
                                </span>
                                </li>

                                <!-- Structure du menu deroulant -->

                                <ul id=\"menu-deroulant\" class=\"dropdown-content\">
                                    <li class=\"valign-wrapper\">
                                        <span class=\"icon-user-md\"></span>
                                        <a href=\"#!\">Mon compte</a>
                                    </li>
                                    <li class=\"divider\"></li>
                                    <li class=\"valign-wrapper\">
                                        <span class=\"icon-off\"></span>
                                        <a href=\"#!\">Déconnexion</a>
                                    </li>
                                </ul>
                                <li><a href=\"#\" class=\"dropdown-button\" data-activates=\"menu-deroulant\"><span class=\"icon-user\"></span> John Smith</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
        {% block body %}{% endblock %}

    <script
            src=\"https://code.jquery.com/jquery-3.3.1.min.js\"
            integrity=\"sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=\"
            crossorigin=\"anonymous\">

    </script>
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js\"></script>
    <script src=\"{{ asset('js/script.js') }}\"></script>
    <script src=\"https://use.fontawesome.com/4f517cd7b4.js\"></script>
    {% block javascripts %}{% endblock %}
    </body>
</html>
", "base.html.twig", "/Users/abiguime/Documents/dev/devspace/phpsymfony/cas/app/Resources/views/base.html.twig");
    }
}
