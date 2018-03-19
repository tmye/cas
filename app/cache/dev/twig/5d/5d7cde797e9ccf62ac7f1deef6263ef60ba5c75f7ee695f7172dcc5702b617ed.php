<?php

/* cas/index.html.twig */
class __TwigTemplate_cde39a07e9dfad7d34fb3445666b81e72f440704274dfe458edb22917999d1e5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "cas/index.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'stylesheets' => array($this, 'block_stylesheets'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_f401d1417830f0bbc39a39e4f6969f618435150a3f9d7d4957c33ff31547046d = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f401d1417830f0bbc39a39e4f6969f618435150a3f9d7d4957c33ff31547046d->enter($__internal_f401d1417830f0bbc39a39e4f6969f618435150a3f9d7d4957c33ff31547046d_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "cas/index.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_f401d1417830f0bbc39a39e4f6969f618435150a3f9d7d4957c33ff31547046d->leave($__internal_f401d1417830f0bbc39a39e4f6969f618435150a3f9d7d4957c33ff31547046d_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_d32c7090a95aa9dc7f07175a84f279f44c3a36ba42cafdd8b4a5cba5baad179c = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_d32c7090a95aa9dc7f07175a84f279f44c3a36ba42cafdd8b4a5cba5baad179c->enter($__internal_d32c7090a95aa9dc7f07175a84f279f44c3a36ba42cafdd8b4a5cba5baad179c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "    <div class=\"row\">
        <div class=\"col l9 offset-l3\">
            <h5>Accueil</h5>

            <!-- Petites sections carrées -->

            <div class=\"row\">
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Retards<span class=\"badge teal lighten-2 white-text\">26</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x red-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Départs<span class=\"badge teal lighten-2 white-text\">13</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-paper-plane fa-4x teal-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Permissions<span class=\"badge teal lighten-2 white-text\">17</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x grey-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Absences<span class=\"badge teal lighten-2 white-text\">26</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x blue-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Section du classement des tops -->

            <div class=\"row\">
                <div class=\"col l6 m6 s12\">
                    <h5>Top retardataires</h5>
                    <br>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                </div>
                <div class=\"col l6 m6 s12\">
                    <h5>Top départs prématurés</h5>
                    <br>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
        
        $__internal_d32c7090a95aa9dc7f07175a84f279f44c3a36ba42cafdd8b4a5cba5baad179c->leave($__internal_d32c7090a95aa9dc7f07175a84f279f44c3a36ba42cafdd8b4a5cba5baad179c_prof);

    }

    // line 129
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_84ca7181a6d17b955f0fb80c2ade9d64b277b06e8cc8c29452bd8d2b315bbcaf = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_84ca7181a6d17b955f0fb80c2ade9d64b277b06e8cc8c29452bd8d2b315bbcaf->enter($__internal_84ca7181a6d17b955f0fb80c2ade9d64b277b06e8cc8c29452bd8d2b315bbcaf_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_84ca7181a6d17b955f0fb80c2ade9d64b277b06e8cc8c29452bd8d2b315bbcaf->leave($__internal_84ca7181a6d17b955f0fb80c2ade9d64b277b06e8cc8c29452bd8d2b315bbcaf_prof);

    }

    public function getTemplateName()
    {
        return "cas/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  171 => 129,  41 => 4,  35 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends 'base.html.twig' %}

{% block body %}
    <div class=\"row\">
        <div class=\"col l9 offset-l3\">
            <h5>Accueil</h5>

            <!-- Petites sections carrées -->

            <div class=\"row\">
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Retards<span class=\"badge teal lighten-2 white-text\">26</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x red-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Départs<span class=\"badge teal lighten-2 white-text\">13</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-paper-plane fa-4x teal-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Permissions<span class=\"badge teal lighten-2 white-text\">17</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x grey-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class=\"col l3 m6 s12\">
                    <ul class=\"collapsible\" data-collapsible=\"accordion\">
                        <li>
                            <div class=\"collapsible-header active\">Absences<span class=\"badge teal lighten-2 white-text\">26</span></div>
                            <div class=\"collapsible-body center\">
                                <a href=\"#\" class=\"fa fa-th-list fa-4x blue-text text-lighten-3\"></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Section du classement des tops -->

            <div class=\"row\">
                <div class=\"col l6 m6 s12\">
                    <h5>Top retardataires</h5>
                    <br>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                </div>
                <div class=\"col l6 m6 s12\">
                    <h5>Top départs prématurés</h5>
                    <br>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                    <div class=\"valignwrapper\" style=\"padding-bottom: 30px\">
                        <i class=\"fa fa-user top\"></i>
                        <span class=\"top user-info-home\">
                            <span>Abessi Francis</span>
                            <br>
                            <span>1h05 min</span>
                            <br>
                            <span>Département informatique</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}

{% block stylesheets %}
{% endblock %}
", "cas/index.html.twig", "/Users/abiguime/Documents/dev/devspace/phpsymfony/cas/app/Resources/views/cas/index.html.twig");
    }
}
