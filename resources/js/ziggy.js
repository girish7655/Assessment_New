import { route as ziggyRoute } from 'ziggy-js';

export const ZiggyVue = {
    install: (app) => {
        app.config.globalProperties.$route = ziggyRoute;
        app.provide('route', ziggyRoute);
    }
};
