# Contributing to the CAcert code base

This short guide will help you to get your contributions into the cacert-devel
code base.

## Checking the bug tracker

CAcert tracks bugs in the bug tracker at https://bugs.cacert.org/. Please look
whether the change you want to contribute addresses any of the issues there.

## Create a fork

You need to authenticate with your GitHub account (or create a new one).

Go to https://github.com/CAcertOrg/cacert-devel and click the *Fork* button in
the Github UI. You may also use the
[GitHub CLI](https://cli.github.com/manual/).

```
gh repo clone CAcertOrg/cacert-devel cacert-devel
cd cacert-devel
gh repo fork --remote
```

## Create a local bugfix branch

Get the latest changes from the original repository and your fork:

```
git fetch --all
```

Create a new bugfix branch based on the upstream/release branch (you may use
origin/release if you just `git clone`d from your own fork).

```
git checkout -b bugfix-<bugnumber> upstream/release
```

## Edit code / documentation

Make sure that you do the minimal required changes to the code or documentation
files, this will make life of reviewers easier.

## Commit your changes

Commit the changes that you made to your local branch. Please provide a
[meaningful commit message](https://chris.beams.io/posts/git-commit/) and
reference the bug tracker URL when you contribute to fix any of the issues.

```
git add .
git commit -m "Fix foo in bla subsystem

This commit does XYZ to address ABC.

https://bugs.cacert.org/view.php?id=<bugnumber>"
```

You may add more commits but please make sure that you only do changes required
for the specific contribution. Please use new branches for other
features/bugfixes.

## Push your changes to your fork

If you needed a while to prepare your changes you should rebase your branch on
the latest changes in the CAcertOrg/cacert-devel release branch:

```
git fetch upstream
git rebase upstream/release
```

Push your bugfix branch to your fork:

```
git push origin bugfix-<bugnumber>
```

## Create a pull request

To get the attention of reviewers you need to create a pull request. You can do
this via the GitHub UI (via the Contribute / Open pull request option in the
branch view of your branch) or via the GitHub CLI:

```
gh pr create -B release -w
```

It may be a good idea to mention your pull request in a comment for the issue
in the bug tracker.

## Other ways of contributing

You may also attach files or patches to issues in the bug tracker. Pull
requests will make reviews easier though.

