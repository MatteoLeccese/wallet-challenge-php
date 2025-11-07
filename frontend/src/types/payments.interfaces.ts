export interface Transactions {
  id: number;
  type: string;
  amount: number | string;
  referenceId: number | null;
  created_at: string;
}

export interface IncomingOutgoingPayments {
  id: number;
  status: string,
  amount: number | string,
  created_at: string,
}

export interface InitiatePurchaseData {
  amount: number;
}

export interface InitiatePurchaseResponse {
  message?: string;
  userId: number;
  walletId: number;
  newBalance: number;
  transactionId: number;
}

export interface ConfirmTransactionData {
  sessionId: number;
  token: string;
}

export interface ConfirmTransactionResponse {
  message?: string,
  sessionId: number;
  amount: number;
  walletId: number;
  newBalance: number;
}

export interface InitiatePaymentData {
  toDocument: string;
  toPhone: string;
  amount: number;
}

export interface InitiatePaymentResponse {
  message?: string;
  sessionId: number;
  amount: number;
  fromWalletId: number;
  toWalletId: number;
}
