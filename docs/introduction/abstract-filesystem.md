# Abstract Filesystem
One of the goals of the Shopsys Framework is to give you tools to make your e-commerce platform scalable.
One of the requirements for scalable application is separated file storage that is accessible from all application instances.
<!--- TODO

Change:
We use abstract filesystem - [Flysystem](https://github.com/thephpleague/flysystem) via [OneUpFlysystemBundle](https://github.com/1up-lab/OneupFlysystemBundle) to fulfill this requirement.

To:
We use abstract filesystem - [Flysystem](https://github.com/thephpleague/flysystem).
--->
We use abstract filesystem - [Flysystem](https://github.com/thephpleague/flysystem) via [OneUpFlysystemBundle](https://github.com/1up-lab/OneupFlysystemBundle) to fulfill this requirement.

## Flysystem
[Flysystem](https://github.com/thephpleague/flysystem) allows you to easily swap out a local filesystem for a remote one like Redis, Amazon S3, Dropbox etc.

### What is Flysystem used for
In Shopsys Framework we currently use [Flysystem](https://github.com/thephpleague/flysystem) to store:
- uploaded files and images
- uploaded files and images via WYSIWYG
- generated feeds
- generated sitemaps

### How to change storage adapter for filesystem
Flysystem supports a huge number of storage adapters. You can find [full list here](https://github.com/thephpleague/flysystem#community-integrations).
<!--- TODO
Change:

You can change the setting of the adapter in `app/config/packages/oneup_flysystem.yml` file.
There you find two adapters by default.
The first one `main_filesystem` is the one that application uses for storing files.
In order to change the storage of your files update the `main_adapter` adapter appropriately.
For more information how to set up adapter visit [OneUpFlysystem documentation](https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/index.md#step3-configure-your-filesystems).

To:
If you want to change the adapter used for Filesystem you must implement factory for `FilesystemFactoryInterface` and register it in `services.yml` file under `main_filesystem` alias.

--->
You can change the setting of the adapter in `app/config/packages/oneup_flysystem.yml` file.
There you find two adapters by default.
The first one `main_filesystem` is the one that application uses for storing files.
In order to change the storage of your files update the `main_adapter` adapter appropriately.
For more information how to set up adapter visit [OneUpFlysystem documentation](https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/index.md#step3-configure-your-filesystems).

#### How to change storage adapter for WYSIWYG
WYSIWYG configuration is stored in `app/config/packages/fm_elfinder.yml` file in `fm_elfinder\instances\default\connector\roots` section.
For more information how to set up Flysystem with WYSIWYG visit [FMElfinderBundle Documentation](https://github.com/helios-ag/FMElfinderBundle/blob/master/Resources/doc/flysystem.md).

#### Create Nginx proxy to load files from different storage
If you changed the file storage, you have to change also loading of these files to be accessible from the frontend of your application.
You need to update your Nginx proxy to access your new storage.

### The Inevitable Exceptions
In some cases, you need to download/upload files to your local filesystem, do some job with them and then upload the result via the abstract filesystem.