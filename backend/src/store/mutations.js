import state from "./state";

export function setUser(state, user) {
    state.user.data = user;
}

export function setToken(state, token) {
    const tokenValue = typeof token === 'object' && token !== null ? token.token : token;
    const remember = typeof token === 'object' && token !== null ? !!token.remember : false;

    state.user.token = tokenValue;
    if (tokenValue) {
        if (remember) {
            localStorage.setItem('TOKEN', tokenValue);
            sessionStorage.removeItem('TOKEN');
        } else {
            sessionStorage.setItem('TOKEN', tokenValue);
            localStorage.removeItem('TOKEN');
        }
    } else {
        sessionStorage.removeItem('TOKEN');
        localStorage.removeItem('TOKEN');
    }
}

export function setProducts(state, [loading, response = null]) {
    if (response) {
        state.products = {
            data: response.data,
            links: response.meta.links,
            total: response.meta.total,
            limit: response.meta.per_page,
            from: response.meta.from,
            to: response.meta.to,
            page: response.meta.current_page,
        }
    }
    state.products.loading = loading;
};

export function setOrders(state, [loading, response = null]) {
    if (response) {
        state.orders = {
            data: response.data,
            links: response.meta.links,
            total: response.meta.total,
            limit: response.meta.per_page,
            from: response.meta.from,
            to: response.meta.to,
            page: response.meta.current_page,
        }
    }
    state.orders.loading = loading;
};

export function setUsers(state, [loading, response = null]) {
    if (response) {
        state.users = {
            data: response.data,
            links: response.meta.links,
            total: response.meta.total,
            limit: response.meta.per_page,
            from: response.meta.from,
            to: response.meta.to,
            page: response.meta.current_page,
        }
    }
    state.users.loading = loading;
};

export function setCustomers(state, [loading, response = null]) {
    if (response) {
        state.customers = {
            data: response.data,
            links: response.meta.links,
            total: response.meta.total,
            limit: response.meta.per_page,
            from: response.meta.from,
            to: response.meta.to,
            page: response.meta.current_page,
        }
    }
    state.customers.loading = loading;
};

export function showToast(state, message) {
    state.toast.show = true;
    state.toast.message = message;
};

export function hideToast(state) {
    state.toast.show = false;
    state.toast.message = '';
};

export function setCountries(state, countries) {
    state.countries = countries.data;
}
