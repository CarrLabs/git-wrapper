<?php
/**
 * Represents a git tag.
 *
 * @author Brent Goldthwaite <brent@carr-labs.com>
 * @license http://http://opensource.org/licenses/bsd-license.php BSD-2-Clause
 * @copyright (c) 2013, Brent Goldthwaite
 */

namespace CarrLabs\GitWrapper;

class GitTag
{
	/**
	 * The name of the tag
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The repository this tag belongs to
	 *
	 * @var GitRepository
	 */
	public $repository;

	/**
	 * The remote repository this tag belongs to, if any
	 *
	 * @var GitRemote
	 */
	public $remote;

	/**
	 * The name of the author of the tag
	 *
	 * @var string
	 */
	protected $_authorName = NULL;

	/**
	 * The email address of the author of the tag
	 *
	 * @var string
	 */
	protected $_authorEmail = NULL;

	/**
	 * The message for this tag
	 *
	 * @var string
	 */
	protected $_message = NULL;

	/**
	 * The commit that this tag points to
	 *
	 * @var GitCommit
	 */
	protected $_commit = NULL;

	/**
	 * Constructor.
	 *
	 * @param string $name the name of the tag
	 * @param GitBranch|null $branch the branch this tag belongs to
	 */
	public function __construct($name, GitRepository $repository, GitRemote $remote = NULL)
	{
		$this->repository = $repository;
		$this->name = $name;
		$this->remote = $remote;
	}

	/**
	 * Loads the metadata for the tag
	 */
	protected function loadData()
	{
		$delimiter = '|||||-----|||||-----|||||';
		$command = 'show --pretty=format:"' . $delimiter . '%H' . $delimiter . '" ' . $this->name;

		$response = explode($delimiter, $this->repository->run($command));
		$tagData = $response[0];
		$commitHash = $response[1];

		if(strpos($tagData, "tag $this->name") === 0)
		{ //annotated tag
			if(preg_match("/Tagger: (.*)\n/", $tagData, $matches))
			{
				$tagger = $matches[1];
				if(preg_match('/(.*) <(.*)>/u', $tagger, $matches))
				{
					$this->_authorEmail = trim(array_pop($matches));
					$this->_authorName = trim(array_pop($matches));
				}
				else
				{
					$this->_authorName = $tagger;
				}

				$this->_message = trim(preg_replace("/.*\nTagger: .*\n/", '', $tagData));
			}
		}

		$this->_commit = new GitCommit($commitHash, $this->repository);
	}

	/**
	 * Returns the name of the author of the tag
	 *
	 * @return string name of tag author
	 */
	public function getAuthorName()
	{
		if(is_null($this->_authorName))
		{
			$this->loadData();
		}
		return $this->_authorName;
	}

	/**
	 * Returns the email address of the author of the tag
	 *
	 * @return string email address of tag author
	 */
	public function getAuthorEmail()
	{
		if(is_null($this->_authorEmail))
		{
			$this->loadData();
		}
		return $this->_authorEmail;
	}

	/**
	 * Returns the tag description
	 *
	 * @return string description of the tag
	 */
	public function getMessage()
	{
		if(is_null($this->_message))
		{
			$this->loadData();
		}
		return $this->_message;
	}

	/**
	 * Gets the commit this tag points to
	 *
	 * @return GitCommit the commit this tag points to
	 */
	public function getCommit()
	{
		if(is_null($this->_commit))
		{
			$this->loadData();
		}
		return $this->_commit;
	}

	/**
	 * Pushes a tag to a remote repository.
	 *
	 * @param null|string|GitRemote $remote the remote repository to push to. If null, falls back to repository $defaultRemote.
	 * @return string the response from git
	 */
	public function push($remote = null)
	{
		if(is_null($remote))
		{
			$remote = $this->repository->remote;
		}

		return $this->repository->run("push $remote tag $this->name");
	}

	/**
	 * @return string tag name
	 */
	public function __toString()
	{
		return $this->name;
	}
}
