import axiosClient from '../axios';

export function getCurrentUser({commit}, data) {
  return axiosClient.get('/user', data)
    .then(({data}) => {
      commit('setUser', data);
      return data;
    })
}

export function login({ commit }, credentials) {
    return axiosClient.post('/login', credentials)
        .then(({ data }) => {
            commit('setUser', data.user);
            commit('setToken', {
                token: data.token,
                remember: !!credentials.remember,
            });
            return data;
        });
}

export function logout({ commit }) {
    return axiosClient.post('/logout')
        .then((response) => {
            commit('setToken', null);
            return response;
        });
}

// products:

export function getProducts({commit}, {url = null, search = '', perPage = 20, sort_field, sort_direction} = {}) {
    commit('setProducts', [true]);
    url = url || '/products';
    return axiosClient.get(url, {
        params: {
            search,
            per_page: perPage,
            sort_field,
            sort_direction
        }
    })
        .then(res => {
            commit('setProducts', [false, res.data]);
        })
        .catch(() => {
            commit('setProducts', [false]);
        });
};

export function getProduct({ }, id) {
    return axiosClient.get(`/products/${id}`)
}

export function createProduct({commit}, product) {
    if (product.images && product.images.length) {
        const form = new FormData();
        form.append('title', product.title);
        product.images.forEach(im => form.append('images[]', im));
        form.append('description', product.description || '');
        form.append('published', product.published ? 1 : 0);
        form.append('price', product.price);
        product = form;
    }
    return axiosClient.post('/products', product)
}

export function updateProduct({commit}, product) {
    const id = product.id
    if (product.images && product.images.length) {
        const form = new FormData();
        form.append('id', product.id);
        form.append('title', product.title);
        product.images.forEach(im => form.append('images[]', im));
        if (product.deleted_images) {
            product.deleted_images.forEach(id => form.append('deleted_images[]', id))
        }
        form.append('description', product.description);
        form.append('published', product.published ? 1 : 0);
        form.append('price', product.price);
        form.append('_method', 'PUT');
        product = form;
    } else {
        product._method = 'PUT'
    }
    return axiosClient.post(`/products/${id}`, product)
}

export function deleteProduct({ commit }, id) {
    return axiosClient.delete(`/products/${id}`)
}

// orders:

export function getOrders({commit}, {url = null, search = '', perPage = 20, sort_field, sort_direction} = {}) {
    commit('setOrders', [true]);
    url = url || '/orders';
    return axiosClient.get(url, {
        params: {
            search,
            per_page: perPage,
            sort_field,
            sort_direction
        }
    })
        .then(res => {
            commit('setOrders', [false, res.data]);
        })
        .catch(() => {
            commit('setOrders', [false]);
        });
};

export function getOrder({ commit }, id) {
    return axiosClient.get(`/orders/${id}`)
}

// users:

export function getUsers({commit}, {url = null, search = '', perPage = 20, sort_field, sort_direction} = {}) {
    commit('setUsers', [true]);
    url = url || '/users';
    return axiosClient.get(url, {
        params: {
            search,
            per_page: perPage,
            sort_field,
            sort_direction
        }
    })
        .then(res => {
            commit('setUsers', [false, res.data]);
        })
        .catch(() => {
            commit('setUsers', [false]);
        });
};

export function updateUser({commit}, user) {
    return axiosClient.put(`/users/${user.id}`, user)
}

export function createUser({commit}, user) {
    return axiosClient.post('/users', user)
}

export function deleteUser({ commit }, user) {
    return axiosClient.delete(`/users/${user.id}`)
}

// customers:

export function getCustomer({commit}, id) {
    return axiosClient.get(`/customers/${id}`)
}

export function updateCustomer({commit}, customer) {
    return axiosClient.put(`/customers/${customer.id}`, customer)
}

export function createCustomer({commit}, customer) {
    return axiosClient.post('/customers', customer)
}

export function deleteCustomer({ commit }, customer) {
    return axiosClient.delete(`/customers/${customer.id}`)
}

export function getCustomers({commit}, {url = null, search = '', perPage = 20, sort_field, sort_direction} = {}) {
    commit('setCustomers', [true]);
    url = url || '/customers';
    return axiosClient.get(url, {
        params: {
            search,
            per_page: perPage,
            sort_field,
            sort_direction
        }
    })
        .then(res => {
            commit('setCustomers', [false, res.data]);
        })
        .catch(() => {
            commit('setCustomers', [false]);
        });
};

// categories:

// countries:

export function getCountries({commit}) {
    return axiosClient.get('countries')
        .then(({ data }) => {
            commit('setCountries', data)
        })
}
