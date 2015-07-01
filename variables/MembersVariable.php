<?php
namespace Craft;

/**
 * OFFICIAL DOCUMENTATION:
 * http://buildwithcraft.com/docs/plugins/variables
 */

class MembersVariable
{

	/**
	 * Whatever you want to output to a Twig tempate
	 * can go into a Variable method.
	 *
	 * HOW TO USE IT
	 * From any Twig template, call it like this:
	 *
	 *     {{ craft.businessLogic.exampleVariable }}
	 *
	 * Or, if your variable requires input from Twig:
	 *
	 *     {{ craft.businessLogic.exampleVariable(twigValue) }}
	 *
	 */
	public function exampleVariable($optional = null)
	{
		return "And away we go to the Twig template...";
	}
	
}
