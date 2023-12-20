import { createApp, defineAsyncComponent } from "vue";
const app = createApp({});

const MainComponent = defineAsyncComponent(() =>
    import("../js/components/MainComponent.vue")
);

app.component("main-component", MainComponent);
app.mount("#app");

