import { Schema, model } from 'mongoose';

export interface UserDocument {
  _id: string;
  email: string;
  passwordHash: string;
  displayName: string;
  createdAt: Date;
  updatedAt: Date;
}

const userSchema = new Schema<UserDocument>(
  {
    email: { type: String, required: true, unique: true, lowercase: true, trim: true },
    passwordHash: { type: String, required: true },
    displayName: { type: String, required: true },
  },
  { timestamps: true }
);

export const UserModel = model<UserDocument>('User', userSchema);
