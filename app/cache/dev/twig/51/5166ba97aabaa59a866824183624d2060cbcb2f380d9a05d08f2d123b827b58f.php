<?php

/* @Twig/Exception/exception_full.html.twig */
class __TwigTemplate_d1362c0159ecd6fcec5714f6334c99362799877afef41c86c0d2c2c6dcfbf8c9 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@Twig/layout.html.twig", "@Twig/Exception/exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Twig/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_e5192f19fdb2679fee892ca19e72702dc30f17f3a61530b4cf0692170339f9e9 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_e5192f19fdb2679fee892ca19e72702dc30f17f3a61530b4cf0692170339f9e9->enter($__internal_e5192f19fdb2679fee892ca19e72702dc30f17f3a61530b4cf0692170339f9e9_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@Twig/Exception/exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_e5192f19fdb2679fee892ca19e72702dc30f17f3a61530b4cf0692170339f9e9->leave($__internal_e5192f19fdb2679fee892ca19e72702dc30f17f3a61530b4cf0692170339f9e9_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_9229802a628e181149b021e97aa0ee79c4e2b423473369a1852ebb74bb6022ac = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_9229802a628e181149b021e97aa0ee79c4e2b423473369a1852ebb74bb6022ac->enter($__internal_9229802a628e181149b021e97aa0ee79c4e2b423473369a1852ebb74bb6022ac_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpFoundationExtension')->generateAbsoluteUrl($this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_9229802a628e181149b021e97aa0ee79c4e2b423473369a1852ebb74bb6022ac->leave($__internal_9229802a628e181149b021e97aa0ee79c4e2b423473369a1852ebb74bb6022ac_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_5f5508db46c5403bf5d08918fabaadc02b9be98a63b6fe2128e85a291460c38a = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_5f5508db46c5403bf5d08918fabaadc02b9be98a63b6fe2128e85a291460c38a->enter($__internal_5f5508db46c5403bf5d08918fabaadc02b9be98a63b6fe2128e85a291460c38a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_5f5508db46c5403bf5d08918fabaadc02b9be98a63b6fe2128e85a291460c38a->leave($__internal_5f5508db46c5403bf5d08918fabaadc02b9be98a63b6fe2128e85a291460c38a_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_497ea26724dd9bbdc0296dd15b9556576f74126908559600fb10aaaeca25e90a = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_497ea26724dd9bbdc0296dd15b9556576f74126908559600fb10aaaeca25e90a->enter($__internal_497ea26724dd9bbdc0296dd15b9556576f74126908559600fb10aaaeca25e90a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("@Twig/Exception/exception.html.twig", "@Twig/Exception/exception_full.html.twig", 12)->display($context);
        
        $__internal_497ea26724dd9bbdc0296dd15b9556576f74126908559600fb10aaaeca25e90a->leave($__internal_497ea26724dd9bbdc0296dd15b9556576f74126908559600fb10aaaeca25e90a_prof);

    }

    public function getTemplateName()
    {
        return "@Twig/Exception/exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@Twig/layout.html.twig' %}

{% block head %}
    <link href=\"{{ absolute_url(asset('bundles/framework/css/exception.css')) }}\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
{% endblock %}

{% block title %}
    {{ exception.message }} ({{ status_code }} {{ status_text }})
{% endblock %}

{% block body %}
    {% include '@Twig/Exception/exception.html.twig' %}
{% endblock %}
", "@Twig/Exception/exception_full.html.twig", "/Users/abiguime/Documents/dev/devspace/phpsymfony/cas/vendor/symfony/symfony/src/Symfony/Bundle/TwigBundle/Resources/views/Exception/exception_full.html.twig");
    }
}
