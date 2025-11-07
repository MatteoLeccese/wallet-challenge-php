import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { LogOut, Wallet, Plus, ShoppingCart, Send, History } from 'lucide-react';
import { useAuthStore } from '../store/authStore';
import { useToastStore } from '../store/toastStore';
import { apiService } from '../services/api';
import { RechargeModal } from '../components/RechargeModal';
import { PurchaseModal } from '../components/PurchaseModal';
import { ConfirmModal } from '../components/ConfirmModal';
import { PaymentModal } from '../components/PaymentModal';
import { TransactionHistory } from '../components/TransactionHistory';
import type { Transactions } from '../types/payments.interfaces';
import { parseIncomingOutgoingToTransactions } from '../utils/utils';

export const Dashboard = () => {
  // Hook to navigate
  const navigate = useNavigate();

  // User and logout function from the store
  const { user, logout } = useAuthStore();

  // Function to show a toast
  const showToast = useToastStore((state) => state.showToast);

  // Balance state
  const [balance, setBalance] = useState<number | string>(0);
  const [availableBalance, setAvailableBalance] = useState<number | string>(0);
  const [pendingBalance, setPendingBalance] = useState<number | string>(0);

  // Transactions state
  const [transactions, setTransactions] = useState<Transactions[]>([]);
  const [pendingTransactions, setPendingTransactions] = useState<Transactions[]>([]);

  // Modals
  const [showRechargeModal, setShowRechargeModal] = useState(false);
  const [showPurchaseModal, setShowPurchaseModal] = useState(false);
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [showHistoryModal, setShowHistoryModal] = useState(false);
  const [showPendingModal, setShowPendingModal] = useState(false);

  // Confirmation data state
  const [confirmDataType, setConfirmDataType] = useState<'purchase' | 'payment' | null>(null);

  // Fetch balance function
  const fetchBalance = useCallback(async () => {
    try {
      const response = await apiService.getBalance();

      if (response.status === 200) {
        // Setting the balance
        setBalance(response.data.balance);
        setAvailableBalance(response.data.available_balance);
        setPendingBalance(response.data.pending_balance);

        // Setting the transactions
        setTransactions(response.data.transactions ?? []);

        let totalPendingTransactions: Transactions[] = [];

        if (response.data.outgoingPayments) {
          totalPendingTransactions = [...totalPendingTransactions, ...parseIncomingOutgoingToTransactions(response.data.outgoingPayments ?? [], 'PAYMENT')];
        }

        if (response.data.outgoingPayments) {
          totalPendingTransactions = [...totalPendingTransactions, ...parseIncomingOutgoingToTransactions(response.data.incomingPayments ?? [], 'CREDIT')];
        }

        // If there are transactions we order them by created_at
        if (totalPendingTransactions.length > 0) {
          totalPendingTransactions.sort(
            (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
          );
        }

        // Setting the pending transactions
        setPendingTransactions(totalPendingTransactions);
      }
    } catch {
      showToast('An error occurred trying to fetch the balance', 'success');
    }
  }, [showToast]);

  const handleLogout = () => {
    logout();
    navigate('/login');
    showToast('User logged out successfully', 'success');
  };

  const handleRechargeSuccess = () => {
    setShowRechargeModal(false);
    fetchBalance();
  };

  const handlePurchaseSuccess = () => {
    setShowPurchaseModal(false);
    setConfirmDataType('purchase');
    setShowConfirmModal(true);
  };

  const handlePaymentSuccess = () => {
    setShowPaymentModal(false);
    setConfirmDataType('payment');
    setShowConfirmModal(true);
  };

  const handleConfirmSuccess = () => {
    setShowConfirmModal(false);
    setConfirmDataType(null);
    fetchBalance();
  };

  useEffect(() => {
    fetchBalance();
  }, [fetchBalance]);

  if (!user) return null;

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      <header className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="bg-primary rounded-lg p-2">
                <Wallet className="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 className="text-xl font-bold text-dark">PayWallet</h1>
                <p className="text-sm text-gray-600">{user.names}</p>
              </div>
            </div>
            <button
              onClick={handleLogout}
              className="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-red-600 transition-colors"
            >
              <LogOut className="w-5 h-5" />
              <span className="hidden sm:inline">Cerrar Sesión</span>
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Balance Card */}
        <div className="wallet-card mb-8 flex flex-col gap-6">
          <div className="flex justify-around text-white font-bold flex-wrap gap-4">
            <div className="text-center">
              <p className="text-sm text-white/70">Disponible</p>
              <p className="text-xl">${availableBalance}</p>
            </div>
            <div className="text-center">
              <p className="text-sm text-white/70">Diferido</p>
              <p className="text-xl">${pendingBalance}</p>
            </div>
            <div className="text-center">
              <p className="text-sm text-white/70">Total</p>
              <p className="text-xl">${balance}</p>
            </div>
          </div>
          <div className="flex justify-center gap-3 flex-wrap">
            <button
              onClick={() => setShowHistoryModal(true)}
              className="flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-white text-sm"
            >
              <History className="w-4 h-4" />
              Historial
            </button>
            <button
              onClick={() => setShowPendingModal(true)}
              className="flex items-center gap-2 px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg transition-colors text-white text-sm"
            >
              <History className="w-4 h-4" />
              Pagos pendientes
            </button>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
          <button
            onClick={() => setShowRechargeModal(true)}
            className="card hover:shadow-xl transition-all duration-200 group"
          >
            <div className="flex items-center gap-4">
              <div className="bg-primary/10 group-hover:bg-primary group-hover:scale-110 transition-all duration-200 rounded-xl p-3">
                <Plus className="w-6 h-6 text-primary group-hover:text-white" />
              </div>
              <div className="text-left">
                <h3 className="font-semibold text-dark">Recargar</h3>
                <p className="text-sm text-gray-600">Añadir fondos</p>
              </div>
            </div>
          </button>

          <button
            onClick={() => setShowPurchaseModal(true)}
            className="card hover:shadow-xl transition-all duration-200 group"
          >
            <div className="flex items-center gap-4">
              <div className="bg-accent/10 group-hover:bg-accent group-hover:scale-110 transition-all duration-200 rounded-xl p-3">
                <ShoppingCart className="w-6 h-6 text-accent group-hover:text-white" />
              </div>
              <div className="text-left">
                <h3 className="font-semibold text-dark">Comprar</h3>
                <p className="text-sm text-gray-600">Realizar compra</p>
              </div>
            </div>
          </button>

          <button
            onClick={() => setShowPaymentModal(true)}
            className="card hover:shadow-xl transition-all duration-200 group"
          >
            <div className="flex items-center gap-4">
              <div className="bg-purple-100 group-hover:bg-purple-500 group-hover:scale-110 transition-all duration-200 rounded-xl p-3">
                <Send className="w-6 h-6 text-purple-600 group-hover:text-white" />
              </div>
              <div className="text-left">
                <h3 className="font-semibold text-dark">Pagar</h3>
                <p className="text-sm text-gray-600">A otro usuario</p>
              </div>
            </div>
          </button>
        </div>

        {/* Info Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="card">
            <h3 className="text-lg font-semibold text-dark mb-3">Información de Cuenta</h3>
            <div className="space-y-2">
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-gray-600">Documento:</span>
                <span className="font-semibold text-dark">{user.document}</span>
              </div>
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-gray-600">Email:</span>
                <span className="font-semibold text-dark">{user.email}</span>
              </div>
              <div className="flex justify-between py-2">
                <span className="text-gray-600">Teléfono:</span>
                <span className="font-semibold text-dark">{user.phone}</span>
              </div>
            </div>
          </div>

          <div className="card bg-gradient-to-br from-primary/5 to-accent/5 border-primary/20">
            <h3 className="text-lg font-semibold text-dark mb-3">Seguridad</h3>
            <p className="text-gray-700 mb-4 leading-relaxed">
              Todas tus transacciones están protegidas con confirmación por token de seguridad.
            </p>
            <div className="flex items-center gap-2 text-primary">
              <div className="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
              <span className="text-sm font-medium">Cuenta verificada</span>
            </div>
          </div>
        </div>
      </main>

      {/* Modals */}
      {showRechargeModal && (
        <RechargeModal
          onClose={() => setShowRechargeModal(false)}
          onSuccess={handleRechargeSuccess}
        />
      )}

      {showPurchaseModal && (
        <PurchaseModal
          onClose={() => setShowPurchaseModal(false)}
          onSuccess={handlePurchaseSuccess}
        />
      )}

      {showPaymentModal && (
        <PaymentModal
          onClose={() => setShowPaymentModal(false)}
          onSuccess={handlePaymentSuccess}
        />
      )}

      {showConfirmModal && confirmDataType && (
        <ConfirmModal
          type={confirmDataType}
          onClose={() =>{
            setShowConfirmModal(false);
            setConfirmDataType(null);
            fetchBalance();
          }}
          onSuccess={handleConfirmSuccess}
        />
      )}

      {showHistoryModal && (
        <TransactionHistory
          onClose={() => setShowHistoryModal(false)}
          transactions={transactions}
        />
      )}
      {showPendingModal && (
        <TransactionHistory
          onClose={() => setShowPendingModal(false)}
          transactions={pendingTransactions}
        />
      )}
    </div>
  );
};