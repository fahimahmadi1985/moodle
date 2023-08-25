# Moodle Server Repo

The Moodle server URL for DCI

▶️ [Development Server](https://dev.education.digitalcareerinstitute.de/)

## Contribution guide
1. Create a Branch: Always work on a feature-specific or issue-specific branch.
2. Pull Request: Make sure to create a pull request to the dev branch for review.


# Setting up Local Moodle with Docker

Easily set up your local Moodle development environment using Docker and Docker Compose.

## Prerequisites

1. Docker & Docker Compose: Ensure you have Docker and Docker Compose installed. If not, follow the instructions in the Docker [Get Started Guide](https://www.docker.com/get-started).

## Steps to Get Moodle Running Locally

1. **Clone the Repository**

    ```bash
    git clone git@github.com:DigitalCareerInstitute/dci-moodle.git
    ```

2. **Change Directory**

    ```bash
    cd dci-moodle
    ```

3. **Launch Services**

    Start Moodle and its dependencies using Docker Compose in detached mode.

    ```bash
    docker-compose up -d
    ```

4. **Access Moodle**

    Open your web browser and navigate to:

    ```
    localhost:8080/moodle
    ```

5. **Complete Installation**

    Follow the on-screen instructions to finalize your Moodle setup.


### Development Tips

- **Viewing Logs**: If you need to inspect logs for a specific service, use:

    ```bash
    docker-compose logs -f <service_name>
    ```

    For example, to view logs of the web service:

    ```bash
    docker-compose logs -f moodle_web
    ```

    To view logs from all services:

    ```bash
    docker-compose logs -f
    ```

- **Stopping Services**: To stop the running services:

    ```bash
    docker-compose down
    ```