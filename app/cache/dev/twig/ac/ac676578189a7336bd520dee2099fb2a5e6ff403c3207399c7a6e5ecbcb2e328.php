<?php

/* @WebProfiler/Collector/router.html.twig */
class __TwigTemplate_0ccd60b7738180acf4ab3be050e7fba7fefdf38ef8ec3dc10c191c560fa5d7be extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/router.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_b3ce9b53f5bfc255edc6fc9825f129dd5a346fce929fa30c206d435309b53952 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_b3ce9b53f5bfc255edc6fc9825f129dd5a346fce929fa30c206d435309b53952->enter($__internal_b3ce9b53f5bfc255edc6fc9825f129dd5a346fce929fa30c206d435309b53952_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/router.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_b3ce9b53f5bfc255edc6fc9825f129dd5a346fce929fa30c206d435309b53952->leave($__internal_b3ce9b53f5bfc255edc6fc9825f129dd5a346fce929fa30c206d435309b53952_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_252e6655bb43a6ba79d5c2307b4834ab581e35a20d3e648fed0211b6a9bcee57 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_252e6655bb43a6ba79d5c2307b4834ab581e35a20d3e648fed0211b6a9bcee57->enter($__internal_252e6655bb43a6ba79d5c2307b4834ab581e35a20d3e648fed0211b6a9bcee57_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        
        $__internal_252e6655bb43a6ba79d5c2307b4834ab581e35a20d3e648fed0211b6a9bcee57->leave($__internal_252e6655bb43a6ba79d5c2307b4834ab581e35a20d3e648fed0211b6a9bcee57_prof);

    }

    // line 5
    public function block_menu($context, array $blocks = array())
    {
        $__internal_f2d9d57731dceefb51e0c437d9137643078f24732073ef64c91f3c39a6096933 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f2d9d57731dceefb51e0c437d9137643078f24732073ef64c91f3c39a6096933->enter($__internal_f2d9d57731dceefb51e0c437d9137643078f24732073ef64c91f3c39a6096933_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 6
        echo "<span class=\"label\">
    <span class=\"icon\">";
        // line 7
        echo twig_include($this->env, $context, "@WebProfiler/Icon/router.svg");
        echo "</span>
    <strong>Routing</strong>
</span>
";
        
        $__internal_f2d9d57731dceefb51e0c437d9137643078f24732073ef64c91f3c39a6096933->leave($__internal_f2d9d57731dceefb51e0c437d9137643078f24732073ef64c91f3c39a6096933_prof);

    }

    // line 12
    public function block_panel($context, array $blocks = array())
    {
        $__internal_b313a6feb4e9a05c8541a7f523bbddbd6252ea53238b53da583e555f9b484f33 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_b313a6feb4e9a05c8541a7f523bbddbd6252ea53238b53da583e555f9b484f33->enter($__internal_b313a6feb4e9a05c8541a7f523bbddbd6252ea53238b53da583e555f9b484f33_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 13
        echo "    ";
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\HttpKernelExtension')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_router", array("token" => (isset($context["token"]) ? $context["token"] : $this->getContext($context, "token")))));
        echo "
";
        
        $__internal_b313a6feb4e9a05c8541a7f523bbddbd6252ea53238b53da583e555f9b484f33->leave($__internal_b313a6feb4e9a05c8541a7f523bbddbd6252ea53238b53da583e555f9b484f33_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/router.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 13,  67 => 12,  56 => 7,  53 => 6,  47 => 5,  36 => 3,  11 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block toolbar %}{% endblock %}

{% block menu %}
<span class=\"label\">
    <span class=\"icon\">{{ include('@WebProfiler/Icon/router.svg') }}</span>
    <strong>Routing</strong>
</span>
{% endblock %}

{% block panel %}
    {{ render(path('_profiler_router', { token: token })) }}
{% endblock %}
", "@WebProfiler/Collector/router.html.twig", "/Users/abiguime/Documents/dev/devspace/phpsymfony/cas/vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Collector/router.html.twig");
    }
}
