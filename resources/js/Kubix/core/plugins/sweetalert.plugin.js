/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — SweetAlert Service (UI FEEDBACK PURE)
 * ════════════════════════════════════════════════════════════════
 * - Wrapper de SweetAlert2
 * - Alerts / Toast / Confirm / Prompt
 * - Adaptación mobile/desktop
 *
 * ❌ Sin Vue plugin
 * ❌ Sin stores
 * ❌ Sin global state
 * ❌ Sin business logic
 * ════════════════════════════════════════════════════════════════
 */

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import { isMobile } from '@/Kubix/core/utils/device'

class SweetAlertService {
  constructor() {
    this._config = {}
    this._ready = false
  }

  // ─────────────────────────────────────────
  // LIFECYCLE
  // ─────────────────────────────────────────
  init() {
    this._ready = true
    return this
  }

  isReady() {
    return this._ready
  }

  // ─────────────────────────────────────────
  // CONFIG INJECTION (OPTIONAL)
  // ─────────────────────────────────────────
  bindConfig(config = {}) {
    this._config = config
  }

  // ─────────────────────────────────────────
  // DEVICE
  // ─────────────────────────────────────────
  isMobile() {
    return isMobile()
  }

  // ─────────────────────────────────────────
  // BASE CONFIG
  // ─────────────────────────────────────────
  baseConfig() {
    const mobile = this.isMobile()

    return {
      allowOutsideClick: false,
      allowEscapeKey: false,
      position: mobile ? 'bottom' : 'center',

      confirmButtonColor: this._config.primary || '#3b82f6',
      cancelButtonColor: this._config.secondary || '#6b7280',

      customClass: {
        popup: mobile
          ? 'rounded-t-[2rem] rounded-b-none w-full'
          : 'rounded-2xl',

        confirmButton: 'px-6 py-2 font-bold text-white rounded-xl',
        cancelButton: 'px-6 py-2 font-bold rounded-xl',
      },
    }
  }

  // ─────────────────────────────────────────
  // ALERTS
  // ─────────────────────────────────────────
  success(title, text = '') {
    return Swal.fire({
      ...this.baseConfig(),
      icon: 'success',
      title,
      text,
    })
  }

  error(title, text = '') {
    return Swal.fire({
      ...this.baseConfig(),
      icon: 'error',
      title,
      text,
    })
  }

  warning(title, text = '') {
    return Swal.fire({
      ...this.baseConfig(),
      icon: 'warning',
      title,
      text,
    })
  }

  info(title, text = '') {
    return Swal.fire({
      ...this.baseConfig(),
      icon: 'info',
      title,
      text,
    })
  }

  // ─────────────────────────────────────────
  // CONFIRM
  // ─────────────────────────────────────────
  confirm(title, text = '', okText = 'OK', cancelText = 'Cancel') {
    return Swal.fire({
      ...this.baseConfig(),
      icon: 'question',
      title,
      text,
      showCancelButton: true,
      confirmButtonText: okText,
      cancelButtonText: cancelText,
      reverseButtons: true,
    })
  }

  // ─────────────────────────────────────────
  // INPUTS
  // ─────────────────────────────────────────
  async prompt(title, placeholder = '') {
    const { value } = await Swal.fire({
      ...this.baseConfig(),
      title,
      input: 'text',
      inputPlaceholder: placeholder,
      showCancelButton: true,
    })

    return value
  }

  async select(title, options = {}) {
    const { value } = await Swal.fire({
      ...this.baseConfig(),
      title,
      input: 'select',
      inputOptions: options,
      showCancelButton: true,
    })

    return value
  }

  // ─────────────────────────────────────────
  // TOAST
  // ─────────────────────────────────────────
  toast(title, icon = 'success', timer = 2500) {
    return Swal.mixin({
      toast: true,
      position: this.isMobile() ? 'bottom' : 'top-end',
      showConfirmButton: false,
      timer,
      timerProgressBar: true,
    }).fire({
      icon,
      title,
    })
  }

  // ─────────────────────────────────────────
  // LOADING
  // ─────────────────────────────────────────
  loading(title = 'Loading...') {
    return Swal.fire({
      ...this.baseConfig(),
      title,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    })
  }

  close() {
    Swal.close()
  }
}

/**
 * FACTORY (USADO POR plugins/index.js)
 */
export function createSweetAlertService() {
  return new SweetAlertService().init()
}