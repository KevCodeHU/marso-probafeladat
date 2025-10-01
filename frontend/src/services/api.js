const API_BASE = '/api'

async function request(path, options = {}) {
  const res = await fetch(API_BASE + path, {
    credentials: 'include',
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    ...options,
  })

  const text = await res.text()
  let json = null
  try {
    json = text ? JSON.parse(text) : null
  } catch (e) {
  }

  if (!res.ok) {
    if (json && json.error) {
      throw new Error(json.error)
    }
    throw new Error(text)
  }
  return json
}

export const api = {
  listProducts: (page = 1, perPage = 20, q = null, filters = {}) => {
    const params = new URLSearchParams()
    params.set('page', page)
    params.set('perPage', perPage)
    if (q) params.set('q', q)
    if (filters.season) params.set('season', filters.season)
    if (filters.diameter) params.set('diameter', filters.diameter)
    return request('/products?' + params.toString(), { method: 'GET' })
  },
  getProduct: (id) => request(`/product/${id}`, { method: 'GET' }),
  getCategories: () => request('/categories', { method: 'GET' }),
  // cart
  getCart: () => request('/cart', { method: 'GET' }),
  addToCart: (productId, quantity = 1) => request('/cart/add', { method: 'POST', body: JSON.stringify({ productId, quantity }) }),
  removeCartItem: (id) => request(`/cart/remove/${id}`, { method: 'DELETE' }),
  clearCart: () => request('/cart/clear', { method: 'DELETE' }),
  // order
  createOrder: (payload) => request('/order', { method: 'POST', body: JSON.stringify(payload) }),
}
