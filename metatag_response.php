<?php

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Extends core response handler class.
 */
class metatag_response extends eResponse
{

	/**
	 * Mute method so that core and other plugins cannot add meta tag via e107::meta().
	 */
	public function addMeta($name = null, $content = null, $extended = array())
	{
		// Allow only if "metatag" plugin wants to add it.
		if(isset($extended['plugin']) && $extended['plugin'] == 'metatag')
		{
			unset($extended['plugin']);
			$this->_addMeta($name, $content, $extended);
		}

		return $this;
	}

	/**
	 * Generic meta information.
	 *
	 * Example usage:
	 * addMeta('og:title', 'My Title');
	 * addMeta(null, 30, array('http-equiv' => 'refresh'));
	 * addMeta(null, null, array('http-equiv' => 'refresh', 'content' => 30)); // same as above
	 *
	 * @param string $name
	 *   'name' attribute value, or null to avoid it
	 * @param string $content
	 *   'content' attribute value, or null to avoid it
	 * @param array $extended
	 *   format 'attribute_name' => 'value'
	 * @return eResponse
	 */
	public function _addMeta($name = null, $content = null, $extended = array())
	{
		$attr = array();

		if(null !== $name)
		{
			if(!in_array($name, $this->_meta_name_only))
			{
				// Giving both should be valid and avoid issues with FB and others.
				$attr['property'] = $name;
			}

			if(!in_array($name, $this->_meta_property_only))
			{
				$attr['name'] = $name;
			}
		}


		if(null !== $content)
		{
			$attr['content'] = $content;
		}
		if(!empty($extended))
		{
			if(!empty($attr))
			{
				$attr = array_merge($attr, $extended);
			}
			else
			{
				$attr = $extended;
			}
		}

		if(!empty($attr))
		{
			// Prevent multiple keyword tags.
			if($name && !in_array($name, $this->_meta_multiple))
			{
				$this->_meta[$name] = $attr;
			}
			// Multiple allowed.
			else
			{
				$this->_meta[] = $attr;
			}
		}

		return $this;
	}

}
