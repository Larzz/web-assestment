
# Dynamic data visualisation web application Assestment

Thank you! Please find below a brief documentation of the assessment along with the tools and technologies utilized. Kindly note that the documentation is concise due to time constraints.

## Technology Used

[Laravel 10](https://laravel.com/) 

- Streamlining Backend Data Processing
- Employed API Route for Seamless Integration
- Implemented a Singular Controller for Efficient Backend Logic Handling

```php

Route::prefix('v1')->group(function() {

    Route::prefix('data')->group(function() {
        Route::post('connect', [ ApiController::class, 'postConnection']);
        Route::post('query', [ ApiController::class, 'postQuery']);
        Route::post('columns', [ ApiController::class, 'getColumnListing']);
    });

});


```

[Vue 3](https://vuejs.org/)

- Elevating Frontend Data Processing Precision
- Leveraged the Fetch API for Seamless Data Retrieval and Handling

```javascript

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

```

[Boostrap](getbootstrap.com/)

- Styling sheet