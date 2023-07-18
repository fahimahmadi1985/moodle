# Moodle Server Repo

The Moodle server URL for DCI

▶️ [Development Server](https://dev.education.digitalcareerinstitute.de/)

## Contributing
1. Create a branch
2. Make a pull request to `dev`


# Local Moodle Installation with Docker

This guide will help you set up a Moodle development environment locally using Docker.

## Prerequisites

1. Docker: If you haven't installed Docker yet, please follow the instructions in the Docker [Get Started Guide](https://www.docker.com/get-started).

## Steps to Run Moodle Locally

Follow these steps to build and run Moodle locally:

1. **Download Moodle**

    Clone the Moodle repository to your local machine.

    ```bash
    git clone git@github.com:DigitalCareerInstitute/dci-moodle.git
    ```

2. **Navigate to the Moodle Directory**

    ```bash
    cd dci-moodle/moodle
    ```

3. **Build Moodle Docker Image**

    Use the following command to build a Docker image. You can replace "moodle-docker" with the name you want.

    ```bash
    docker build -t moodle-docker .
    ```

4. **Run MySQL Container**

    We will use a MySQL container as our Moodle database. Run the following command, The values `mysql_root_password`, `moodle_db`, `moodle_user`, and `moodle_password` can be changed only if you also edit the cofig.php. For easy installation, don't change them:

    ```bash
    docker run --name moodle_db -e MYSQL_ROOT_PASSWORD=mysql_root_password -e MYSQL_DATABASE=moodle_db -e MYSQL_USER=moodle_user -e MYSQL_PASSWORD=moodle_password -d mysql:latest
    ```

5. **Run Moodle App**

    Now, we will run the Moodle container. This container will link to the MySQL container we created in the previous step:

    ```bash
    docker run --name moodle_app --link moodle_db:mysql -p 8080:80 -d moodle-docker
    ```

6. **Open Moodle in a Browser**

    You should now be able to access your local Moodle instance by navigating to:

    ```bash
    localhost:8080/moodle
    ```

7. **Follow the Moodle Installation Instructions**

    Follow the instructions in the browser to finish your Moodle installation.

## Developing with Docker

If you make changes to your Moodle codebase and want to see those changes reflected in your Docker container, you will need to rebuild the Docker image and rerun the Moodle container:

```bash
docker build -t moodle-docker .
docker run --name moodle_app --link moodle_db:mysql -p 8080:80 -d moodle-docker

