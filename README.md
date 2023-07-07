# Program Goal

This program has been created to manage the creation and deletion of Vultr server instances on demand in order to avoid
being billed unnecessarily. I developed it to facilitate the setup of a development environment accessible through X2GO
from my Raspberry Pi client.

There are two available scripts:

- **CreateInstance**
- **DestroyServer**

## CreateInstance

This script creates a new server instance and populates it with a specific snapshot. To automate the process, it
searches for a snapshot with a configurable label specified in the `.env` file of this project. The label is specified 
in the environment variable **SNAPSHOT_LABEL**, and it uses the **INSTANCE_LABEL** environment variable to label the newly created instance.

To create the new instance, the script requires two mandatory environment parameters:

- **INSTANCE_PLAN**: This parameter represents the plan ID. \
  You can find the suitable plan ID by referring to the
  following link: [Vultr Plans](https://www.vultr.com/api/#operation/list-plans). \
  The new instance will be built based on the plan ID specified.
- **INSTANCE_REGION**: This parameter represents the region ID, which indicates the region where the new instance will
  be hosted by Vultr.

### DestroyServer

This script is responsible for the following tasks:

- Creating a new snapshot of the active instance
- Destroying the old snapshot
- Destroying the server instance

It uses the **INSTANCE_LABEL** to identify the current instance and obtain a snapshot from it, and it uses the 
**SNAPSHOT_LABEL** to find the old snapshot and label the new one accordingly.


# Usage

To create an instance : ```php src/Application.php CreateInstance``` \
To create a snapshot and destroy server instance : ```php src/Application.php DestroyServer```

IF you have docker installed you can use these commands :

To create an instance : ```composer instance_up``` \
To create a snapshot and destroy server instance : ```composer instance_down```