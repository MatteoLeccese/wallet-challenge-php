import type { IncomingOutgoingPayments, Transactions } from './payments.interfaces';

export interface BalanceResponse {
  userId: number;
  walletId: number;
  balance: number | string;
  available_balance: number | string;
  pending_balance: number | string;
  transactions: Transactions[];
  outgoingPayments: IncomingOutgoingPayments[];
  incomingPayments: IncomingOutgoingPayments[];
}

export interface TopUpWalletData {
  document: string;
  phone: string;
  amount: number;
}

export interface TopUpWalletResponse {
  message?: string;
  userId: number;
  walletId: number;
  newBalance: number | string;
  transactionId: number;
}
