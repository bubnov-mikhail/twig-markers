# twig-markers

# Description

```
Twig extention filter allows you replace placeholders 
in text by context of the content.
You MUST extend base class, implement "findContext" method 
and may add as many "onMarker***" 
(where *** is name of placeholder) as you want.
In twig template you should pass Entity object 
as a context of the text with placeholders.
In "onMarker***" method you may check or/and request 
require context from provided one.
In "findContext" method you must check if provided context 
is a requested one or may try find it from provided (see example)
```
# Installation

```
Add require to your composer.json:
```
```json
"bubnovKelnik/twig-markers": "master"
```

```
Edit app/config/services.yml:
```
```yaml
twig.markers:
        class: Path to your extended class
        tags:
            - { name: twig.extension }
```

# Usage examples

```
Lets say we have App\ProjectBundle\Entity\Project.
It has some App\ReportBundle\Entity\Report (s).
Each of Reports has some App\ReportBundle\Entity\Widget (s).

If you provide App\ReportBundle\Entity\Widget as a context, this Entity 
should have methods to find it's parent Report, and/or it's parent Project.
Lets Project has "site" property and you want 
to insert Projects's site url into Widgets's text.
Widgets's text is:

```
#Widget's text:
```text
This is widget text for project %link%. Click it!
```

#Twig:

```twig
<div class="widget-text">{{ widget.text | markers(widget) | raw}}</div>
```

#PHP:
```php
private function onMarkerLink()
{
	if($context = $this->findContext('App\ProjectBundle\Entity\Project'))
	{
	    return $context->getLink();
	}

	return false;
}

private function findContext($findContext)
{
        $contextClass = get_class($this->context);
        if($contextClass === $findContext)
        {
            return $this->context;
        }
	
	//Try to find requested context from provided
	switch($contextClass)
	{
            case 'App\ReportBundle\Entity\Widget':
                switch($findContext)
                {
                    case 'App\ReportBundle\Entity\Report':
                        return $this->context->getReport();
                        break;
                    case 'App\ProjectBundle\Entity\Project':
                        return $this->context->getReport()->getProject();
                        break;
                }
                break;
            case 'App\ReportBundle\Entity\Report':
                switch($findContext)
                {
                    case 'App\ProjectBundle\Entity\Project':
                        return $this->context->getProject();
                        break;
                }
                break;
	}
        return false;
}
```
