#### Introduction

A wrapper for the git command line executable, based on [YiiGit](https://github.com/phpnode/YiiGit).  Allows access to all git commands via a simple object oriented interface.

#### Usage examples

Add some files and folders to git
```PHP
$repository = new \CarrLabs\GitWrapper\GitRepository('path/to/your/git/repo');
$repository->add('somefile.txt');
$repository->add('somedirectory');
```

Commit some files with git
```PHP
$repository->commit('Added some files');
```

Checkout an existing branch
```PHP
$repository->checkout('some-existing-branch');
echo $repository->getActiveBranch()->name; // some-existing-branch
```

Checkout a new branch
```PHP
$repository->checkout('some-new-branch', TRUE);
echo $repository->getActiveBranch()->name; // some-new-branch
```

List all branches
```PHP
foreach($repository->getBranches() as $branch)
{
	echo $branch->name . "\n";
}
```

List all tags with metadata
```PHP
foreach($repository->getTags() as $tag)
{
	echo $tag->name . "\n";
	echo $tag->getAuthorName() . "\n";
	echo $tag->getAuthorEmail() . "\n";
	echo $tag->getMessage() . "\n";
}
```

List all the commits on the current branch
```PHP
foreach($repository->getActiveBranch()->getCommits() as $commit)
{
	echo $commit->getAuthorName() . ' at ' . $commit->getTime() . "\n";
	echo $commit->getMessage() . "\n";
	echo str_repeat('-', 50) . "\n";
}
```

List all the files affected by the latest commit
```PHP
foreach($repository->getActiveBranch()->getLastCommit()->getFiles() as $file)
{
	echo $file . "\n";
}
```

Check if a tag exists on the default remote ('origin')
```PHP
$repository->getRemote()->hasTag('myTag');
```

List all branches on a remote repository called 'upstream'
```PHP
foreach($repository->getRemote('upstream')->getBranches() as $branch)
{
	echo $branch . "\n";
}
```

#### API

\CarrLabs\GitWrapper\GitRepository
```PHP
setPath($path, $createIfEmpty = false, $initialize = false)
getPath()
run($command)
add($file)
rm($file, $force = false)
commit($message = null, $addFiles = false, $amend = false)
status()
describe($options = '')
checkout($branchName, $create = false, $force = false)
clean($deleteDirectories = false, $force = false)
cloneTo($targetDirectory)
cloneFrom($targetDirectory)
cloneRemote($sourceUrl)
push($remote, $branch = "master", $force = false)
fetch($repository)
getActiveBranch()
getBranches()
hasBranch($branch)
createBranch($branchName)
deleteBranch($branchName, $force = false)
getCommit($hash)
getTags()
getTag($name)
hasTag($tag)
addTag($name, $message, $hash = null)
removeTag($tag)
getRemotes()
getRemote($remote = null)
hasCommit($hash)
```

\CarrLabs\GitWrapper\GitCommit
```PHP
getAuthorName()
getAuthorEmail()
getTime()
getSubject()
getMessage()
getNotes()
getParents()
getFiles()
```

\CarrLabs\GitWrapper\GitBranch
```PHP
getCommits()
getCommit($hash)
getLastCommit()
```

\CarrLabs\GitWrapper\GitTag
```PHP
push($remote = null)
getAuthorName()
getAuthorEmail()
getMessage()
getCommit()
```

\CarrLabs\GitWrapper\GitRemote
```PHP
getBranches()
hasBranch($branch)
deleteBranch($branchName)
getTags()
hasTag($tag)
```
