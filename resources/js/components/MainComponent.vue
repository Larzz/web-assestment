<script setup>
    import {
        Bar
    } from 'vue-chartjs'
    import {
        Chart as ChartJS,
        Title,
        Tooltip,
        Legend,
        BarElement,
        CategoryScale,
        LinearScale
    } from 'chart.js'
    ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

    import {
        onMounted,
        ref
    } from 'vue'

    const isLoadedChart = ref(false)
    const response = ref(null)

    const chartOptions = ref({
        response: true
    })

const chartData = ref({
  labels: [],
  datasets: [
    {
      label: 'Data from API',
      backgroundColor: '#f87979',
      data: [],
    },
  ],
});

    const mySqlData = ref({
        host: '65826cd8.dorsy.net',
        username: 'd03f03e4',
        database: 'd03f03e4',
        password: 'Th24WGjFh4f2oNzmv2W3'
    })


    const isConnected = ref(false) // defines if its connected or not
    const mySqlQuery = ref('Select * from Daily_Email_Log')
    const errorMessage = ref('Not Connected')

    const tables = ref({})
    const columns = ref({})

    const isSubmit = ref(false)

    /**
     * Handle data connection submission.
     *
     * @param string $host The database host.
     * @param string $username The database username.
     * @param string $database The database name.
     * @param string $password The database password.
     * @return bool $status The boolean status indicating the success of the data connection submission.
     */
    const onSubmitDataConnection = async () => {

        if (!mySqlData.value.host) {
            return false
        }

        if (!mySqlData.value.username) {
            return false
        }

        if (!mySqlData.value.database) {
            return false
        }

        if (!mySqlData.value.password) {
            return false
        }

        const response = await fetch(`api/v1/data/connect`, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(mySqlData.value),
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("HTTP status " + response.status);
                }
                isSubmit.value = false
                return response.json();
            })
            .then((response) => {
                if (response.status) {
                    tables.value = response.tables
                    isConnected.value = true
                } else {
                    isConnected.value = false
                    errorMessage.value = response.message
                }
            })
            .catch((error) => {
                alert(error)
            });

    }

    const dataResult = ref({})

    /**
     * Handles a database query.
     *
     * @param string $query The database query to be handled.
     * @return object $result The result object obtained from the database query.
     */
    const onSubmitQuery = async () => {

        if (isConnected.value == false) {
            return false
        }

        if (!mySqlQuery.value) {
            return false
        }

        const response = await fetch(`api/v1/data/query`, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    connection: mySqlData.value,
                    query: mySqlQuery.value
                }),
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("HTTP status " + response.status);
                }
                isSubmit.value = false
                return response.json();
            })
            .then((response) => {
                dataResult.value = response.result
                isLoadedChart.value = true
                chartData.value.labels = columns.value;
                chartData.value.datasets[0].data = response.result;

            })
            .catch((error) => {
                alert(error)
            });

    }


    const tableName = ref('')
    /**
     * Handles to get the columns.
     *
     * @param string $str The database query to be handled.
     * @return object $result The result object obtained from the database query.
     */
    const onGetTableColumns = async (str) => {

        tableName.value = str // reuse the string
        const response = await fetch(`api/v1/data/columns`, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    tableName: tableName.value,
                    connection: mySqlData.value
                }),
            })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("HTTP status " + response.status);
                }
                isSubmit.value = false
                return response.json();
            })
            .then((response) => {
                if (response.status) {
                    columns.value = response.columns
                }
            })
            .catch((error) => {
                alert(error)
            });
    }

</script>

<template>
    <div class="container">
        <main>
            <div class="row g-5">
                <div class="col-md-12 col-lg-12">
                    <h4 class="mb-3 mt-5">MySql Data</h4>

                    <div class="col-md-12" v-if="isConnected">
                        <div class="alert alert-success">
                            Connected
                        </div>
                    </div>

                    <div class="col-md-12" v-if="!isConnected">
                        <div class="alert alert-warning">
                            {{ errorMessage }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <form action="javascript:;" novalidate>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <input type="text" v-model="mySqlData.host" placeholder="Host"
                                            class="form-control" @blur="onSubmitDataConnection" id="host" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" v-model="mySqlData.database" placeholder="Database"
                                            class="form-control" @blur="onSubmitDataConnection" id="database" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" v-model="mySqlData.username" placeholder="Username"
                                            class="form-control" @blur="onSubmitDataConnection" id="username" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" v-model="mySqlData.password" placeholder="Password"
                                            class="form-control" @blur="onSubmitDataConnection" id="password" required>
                                    </div>
                                </div>
                                <hr class="my-4">
                            </form>
                            <h4 class="mb-3 mt-5">MySql Query</h4>
                            <form action="javascript:;">
                                <div class="col-sm-12">
                                    <textarea name="" v-model="mySqlQuery" class="form-control" id="" cols="30"
                                        rows="10"></textarea>
                                </div>
                                <div class="col-md-12 mt-2 text-end">
                                    <button class=" btn btn-primary" @click="onSubmitQuery"
                                        type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <ul class="list-group">
                                <li @click="onGetTableColumns(table)" class="cursorHand list-group-item"
                                    v-for="(table, index) in tables" :key="index">{{ table }}</li>
                            </ul>

                            <ul class="list-group mt-3" v-if="tableName">
                                <li class="list-group-item list-group-item-warning">{{ tableName }}</li>
                                <li class="list-group-item list-group-item-primary" v-for="(column, index) in columns"
                                    :key="index">{{ column }}</li>
                            </ul>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="col-md-12">
                        <table id="table-responsive" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th v-for="(header, index) in columns" :key="index">{{ header }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, rowIndex) in dataResult" :key="rowIndex">
                                    <td v-for="(value, colIndex) in columns" :key="colIndex">{{ item[value] }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <Bar id="my-chart-id" v-if="isLoadedChart" :data="chartData" />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>


<style>
    .container {
        max-width: 960px;
    }

    .cursorHand {
        cursor: pointer;
    }

</style>
