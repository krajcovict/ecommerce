const state = {
        user: {
            token: sessionStorage.getItem('TOKEN') || null,
            data: null,
    },
    products: {
        loading: false,
        data: [],
    },
    };

export default state;
