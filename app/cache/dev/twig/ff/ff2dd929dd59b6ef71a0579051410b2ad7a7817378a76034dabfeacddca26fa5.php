<?php

/* DeviceManagerBundle:Default:index.html.twig */
class __TwigTemplate_e806d802711853bf09b3fcecefea96e9f8150bb81c1dec81b0f86d99bdeb3f50 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_21f8fe745603d41344286430bedaf456acde7c4795cb70bce6e3d6b4fe869d75 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_21f8fe745603d41344286430bedaf456acde7c4795cb70bce6e3d6b4fe869d75->enter($__internal_21f8fe745603d41344286430bedaf456acde7c4795cb70bce6e3d6b4fe869d75_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "DeviceManagerBundle:Default:index.html.twig"));

        // line 1
        echo "Hello World!
";
        
        $__internal_21f8fe745603d41344286430bedaf456acde7c4795cb70bce6e3d6b4fe869d75->leave($__internal_21f8fe745603d41344286430bedaf456acde7c4795cb70bce6e3d6b4fe869d75_prof);

    }

    public function getTemplateName()
    {
        return "DeviceManagerBundle:Default:index.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  22 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("Hello World!
", "DeviceManagerBundle:Default:index.html.twig", "/Users/abiguime/Documents/dev/devspace/phpsymfony/cas/src/DeviceManagerBundle/Resources/views/Default/index.html.twig");
    }
}
